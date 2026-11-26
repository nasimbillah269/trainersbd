<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseOrder;
use App\Models\CourseOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Reports Management, built on course orders (course_orders / course_order_items).
 * The old reports read the legacy e-commerce `orders` / `posts` tables, which the course checkout never writes to.
 */
class CourseReportController extends Controller
{
    public function show(Request $r, string $type)
    {
        return match ($type) {
            'products'         => $this->courses($r),
            'order-reports'    => $this->orders($r),
            'customer-reports' => $this->customers($r),
            default            => $this->summary($r),
        };
    }

    /**
     * Resolve the date range from ?range= preset or ?startDate / ?endDate.
     * Returns [from, to] as Carbon (start/end of day) or [null, null] for "all time".
     */
    private function range(Request $r, string $default): array
    {
        $preset = $r->input('range', ($r->startDate || $r->endDate) ? 'custom' : $default);

        return match ($preset) {
            'today' => [today(), today()->endOfDay()],
            '7d'    => [today()->subDays(6), today()->endOfDay()],
            '30d'   => [today()->subDays(29), today()->endOfDay()],
            'month' => [now()->startOfMonth(), today()->endOfDay()],
            'year'  => [now()->startOfYear(), today()->endOfDay()],
            'all'   => [null, null],
            default => [
                $this->parseDate($r->startDate)?->startOfDay(),
                $this->parseDate($r->endDate)?->endOfDay(),
            ],
        };
    }

    private function parseDate($value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function applyRange($q, $from, $to, string $column = 'created_at')
    {
        return $q->when($from, fn ($qq) => $qq->where($column, '>=', $from))
                 ->when($to, fn ($qq) => $qq->where($column, '<=', $to));
    }

    /* ---------------- Summary ---------------- */

    private function summary(Request $r)
    {
        [$from, $to] = $this->range($r, 'month');
        $type = 'summery';

        $orders = $this->applyRange(CourseOrder::query(), $from, $to)
            ->get(['id', 'total', 'discount', 'status', 'payment_status', 'payment_method', 'mobile', 'created_at']);

        $active = $orders->where('status', '!=', 'cancelled');
        $report = [
            'sales'        => $active->sum('total'),                                         // all non-cancelled orders
            'revenue'      => $orders->where('status', 'confirmed')->sum('total'),           // confirmed = money received
            'due'          => $orders->whereIn('status', ['pending', 'hold'])->sum('total'), // waiting for confirmation
            'cancelled_amt'=> $orders->where('status', 'cancelled')->sum('total'),
            'discount'     => $active->sum('discount'),
            'orders'       => $orders->count(),
            'pending'      => $orders->where('status', 'pending')->count(),
            'confirmed'    => $orders->where('status', 'confirmed')->count(),
            'hold'         => $orders->where('status', 'hold')->count(),
            'cancelled'    => $orders->where('status', 'cancelled')->count(),
            'verified'     => $orders->where('payment_status', 'verified')->count(),
            'unverified'   => $orders->where('payment_status', 'unverified')->count(),
            'invalid'      => $orders->where('payment_status', 'invalid')->count(),
            'students'     => $orders->where('status', 'confirmed')->unique('mobile')->count(),
        ];
        $report['avg'] = $report['confirmed'] ? $report['revenue'] / $report['confirmed'] : 0;

        $methods = $active->groupBy('payment_method')->map(fn ($g, $m) => [
            'method' => $m, 'orders' => $g->count(), 'amount' => $g->sum('total'),
            'confirmed' => $g->where('status', 'confirmed')->sum('total'),
        ])->sortByDesc('amount')->values();

        $topCourses = $this->applyRange(
                CourseOrderItem::join('course_orders', 'course_orders.id', '=', 'course_order_items.order_id')
                    ->where('course_orders.status', '!=', 'cancelled'),
                $from, $to, 'course_orders.created_at')
            ->select('course_order_items.course_id', DB::raw('MAX(course_order_items.title) title'), DB::raw('COUNT(*) sales'),
                DB::raw("SUM(CASE WHEN course_orders.status='confirmed' THEN course_order_items.price ELSE 0 END) revenue"))
            ->groupBy('course_order_items.course_id')->orderByDesc('sales')->take(5)->get();

        // Daily breakdown (only when a bounded range of up to ~3 months)
        $daily = collect();
        $start = $from ?: ($orders->min('created_at') ? Carbon::parse($orders->min('created_at'))->startOfDay() : null);
        $end = $to ?: now()->endOfDay();
        if ($start && $start->diffInDays($end) <= 92) {
            $byDay = $orders->groupBy(fn ($o) => $o->created_at->toDateString());
            for ($d = $start->copy()->startOfDay(); $d <= $end; $d->addDay()) {
                $g = $byDay->get($d->toDateString(), collect());
                $daily->push([
                    'date'      => $d->copy(),
                    'orders'    => $g->count(),
                    'confirmed' => $g->where('status', 'confirmed')->count(),
                    'sales'     => $g->where('status', '!=', 'cancelled')->sum('total'),
                    'revenue'   => $g->where('status', 'confirmed')->sum('total'),
                ]);
            }
        }

        return view('admin.reports.summeryReports', compact('type', 'from', 'to', 'report', 'methods', 'topCourses', 'daily'));
    }

    /* ---------------- Course (product) report ---------------- */

    private function courses(Request $r)
    {
        [$from, $to] = $this->range($r, 'all');
        $type = 'products';

        $sales = $this->applyRange(
                CourseOrderItem::join('course_orders', 'course_orders.id', '=', 'course_order_items.order_id'),
                $from, $to, 'course_orders.created_at')
            ->select('course_order_items.course_id',
                DB::raw("SUM(course_orders.status != 'cancelled') orders"),
                DB::raw("SUM(course_orders.status = 'confirmed') confirmed"),
                DB::raw("SUM(course_orders.status IN ('pending','hold')) pending"),
                DB::raw("SUM(course_orders.status = 'cancelled') cancelled"),
                DB::raw("SUM(CASE WHEN course_orders.status='confirmed' THEN course_order_items.price ELSE 0 END) revenue"),
                DB::raw("SUM(CASE WHEN course_orders.status IN ('pending','hold') THEN course_order_items.price ELSE 0 END) due"))
            ->groupBy('course_order_items.course_id')->get()->keyBy('course_id');

        $courses = Course::with('category:id,name')
            ->when($r->category, fn ($q) => $q->where('category_id', $r->category))
            ->when(in_array($r->course_type, ['paid', 'free']), fn ($q) => $q->where('type', $r->course_type))
            ->when(in_array($r->status, ['active', 'draft']), fn ($q) => $q->where('status', $r->status))
            ->when($r->search, fn ($q) => $q->where('title', 'like', '%' . trim($r->search) . '%'))
            ->get(['id', 'title', 'slug', 'category_id', 'type', 'price', 'status'])
            ->map(function ($c) use ($sales) {
                $s = $sales->get($c->id);
                foreach (['orders', 'confirmed', 'pending', 'cancelled', 'revenue', 'due'] as $k) {
                    $c->{'r_' . $k} = (float) ($s->$k ?? 0);
                }
                return $c;
            });

        $courses = match ($r->sort) {
            'title'  => $courses->sortBy('title'),
            'orders' => $courses->sortByDesc('r_orders'),
            default  => $courses->sortByDesc(fn ($c) => [$c->r_revenue, $c->r_orders]),
        };
        $courses = $courses->values();

        if ($r->export === 'csv') {
            return $this->csv('course-report', ['Course', 'Category', 'Type', 'Price', 'Orders', 'Confirmed', 'Pending/Hold', 'Cancelled', 'Revenue (BDT)', 'Due (BDT)'],
                $courses->map(fn ($c) => [$c->title, $c->category->name ?? '', $c->type, $c->price, $c->r_orders, $c->r_confirmed, $c->r_pending, $c->r_cancelled, $c->r_revenue, $c->r_due]));
        }

        $categories = CourseCategory::orderBy('name')->get(['id', 'name']);

        return view('admin.reports.productReports', compact('type', 'from', 'to', 'courses', 'categories'));
    }

    /* ---------------- Order report ---------------- */

    private function orders(Request $r)
    {
        [$from, $to] = $this->range($r, 'all');
        $type = 'order-reports';

        $query = $this->applyRange(CourseOrder::query(), $from, $to)
            ->when(array_key_exists($r->status, CourseOrder::STATUSES), fn ($q) => $q->where('status', $r->status))
            ->when(array_key_exists($r->payment, CourseOrder::PAYMENT_STATUSES), fn ($q) => $q->where('payment_status', $r->payment))
            ->when(in_array($r->method, ['bkash', 'nagad', 'bank']), fn ($q) => $q->where('payment_method', $r->method))
            ->when($r->search, function ($q) use ($r) {
                $s = trim($r->search);
                $q->where(fn ($qq) => $qq->where('invoice', 'like', "%$s%")->orWhere('name', 'like', "%$s%")
                    ->orWhere('mobile', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('transaction_id', 'like', "%$s%"));
            });

        $totals = (clone $query)->selectRaw("COUNT(*) orders, COALESCE(SUM(total),0) amount, COALESCE(SUM(discount),0) discount,
                COALESCE(SUM(CASE WHEN status='confirmed' THEN total ELSE 0 END),0) revenue,
                COALESCE(SUM(CASE WHEN status IN ('pending','hold') THEN total ELSE 0 END),0) due")->first();

        if ($r->export === 'csv') {
            return $this->csv('order-report', ['Invoice', 'Date', 'Name', 'Mobile', 'Email', 'Courses', 'Method', 'Sender', 'TrxID', 'Subtotal', 'Discount', 'Total', 'Status', 'Payment'],
                (clone $query)->with('items:id,order_id,title')->latest()->get()->map(fn ($o) => [
                    $o->invoice, $o->created_at->format('Y-m-d H:i'), $o->name, $o->mobile, $o->email, $o->items->pluck('title')->implode('; '),
                    $o->methodName(), $o->sender_number, $o->transaction_id, $o->subtotal, $o->discount, $o->total,
                    CourseOrder::STATUSES[$o->status] ?? $o->status, CourseOrder::PAYMENT_STATUSES[$o->payment_status] ?? $o->payment_status,
                ]));
        }

        $orders = $query->withCount('items')->latest()->paginate(50)->withQueryString();

        return view('admin.reports.orderReports', compact('type', 'from', 'to', 'orders', 'totals'));
    }

    /* ---------------- Customer (student) report ---------------- */

    private function customers(Request $r)
    {
        [$from, $to] = $this->range($r, 'all');
        $type = 'customer-reports';

        $query = $this->applyRange(CourseOrder::query(), $from, $to)
            ->select('mobile', DB::raw('MAX(name) name'), DB::raw('MAX(email) email'), DB::raw('MAX(user_id) user_id'),
                DB::raw('COUNT(*) orders'), DB::raw("SUM(status='confirmed') confirmed"), DB::raw("SUM(status IN ('pending','hold')) pending"),
                DB::raw("SUM(CASE WHEN status='confirmed' THEN total ELSE 0 END) spent"),
                DB::raw("SUM(CASE WHEN status IN ('pending','hold') THEN total ELSE 0 END) due"),
                DB::raw('MIN(created_at) first_order'), DB::raw('MAX(created_at) last_order'))
            ->when($r->search, function ($q) use ($r) {
                $s = trim($r->search);
                $q->where(fn ($qq) => $qq->where('name', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
            })
            ->groupBy('mobile')
            ->when($r->buyer === 'confirmed', fn ($q) => $q->havingRaw("SUM(status='confirmed') > 0"))
            ->when($r->buyer === 'none', fn ($q) => $q->havingRaw("SUM(status='confirmed') = 0"));

        $query = $r->sort === 'spent' ? $query->orderByDesc('spent') : ($r->sort === 'orders' ? $query->orderByDesc('orders') : $query->orderByDesc('last_order'));

        if ($r->export === 'csv') {
            return $this->csv('customer-report', ['Name', 'Mobile', 'Email', 'Account', 'Orders', 'Confirmed', 'Pending/Hold', 'Spent (BDT)', 'Due (BDT)', 'First order', 'Last order'],
                $query->get()->map(fn ($c) => [$c->name, $c->mobile, $c->email, $c->user_id ? 'Yes' : 'No', $c->orders, $c->confirmed, $c->pending, $c->spent, $c->due, $c->first_order, $c->last_order]));
        }

        $all = (clone $query)->get();
        $totals = [
            'customers' => $all->count(),
            'buyers'    => $all->where('confirmed', '>', 0)->count(),
            'spent'     => $all->sum('spent'),
            'due'       => $all->sum('due'),
        ];
        $customers = $query->paginate(50)->withQueryString();

        return view('admin.reports.customerReports', compact('type', 'from', 'to', 'customers', 'totals'));
    }

    private function csv(string $name, array $head, $rows)
    {
        return response()->streamDownload(function () use ($head, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Bangla names correctly
            fputcsv($out, $head);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'trainersbd-' . $name . '-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
