<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\CourseOrderItem;
use App\Models\CourseReview;
use App\Support\Storefront;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseOrderController extends Controller
{
    public function dashboard()
    {
        $paidStatuses = ['confirmed'];

        $stats = [
            'revenue'        => CourseOrder::whereIn('status', $paidStatuses)->sum('total'),
            'revenue_month'  => CourseOrder::whereIn('status', $paidStatuses)->where('created_at', '>=', now()->startOfMonth())->sum('total'),
            'orders'         => CourseOrder::count(),
            'orders_today'   => CourseOrder::whereDate('created_at', today())->count(),
            'pending'        => CourseOrder::where('status', 'pending')->count(),
            'unverified'     => CourseOrder::where('payment_status', 'unverified')->whereIn('status', ['pending', 'hold'])->count(),
            'courses'        => Course::count(),
            'courses_active' => Course::where('status', 'active')->count(),
            'students'       => CourseOrder::where('status', 'confirmed')->distinct('mobile')->count('mobile'),
            'reviews_pending'=> CourseReview::where('status', 'pending')->count(),
        ];

        // Last 14 days: confirmed revenue and order count
        $from = now()->subDays(13)->startOfDay();
        $rows = CourseOrder::where('created_at', '>=', $from)
            ->selectRaw("DATE(created_at) d, COUNT(*) orders, SUM(CASE WHEN status='confirmed' THEN total ELSE 0 END) revenue")
            ->groupBy('d')->get()->keyBy('d');
        $chart = [];
        for ($i = 0; $i < 14; $i++) {
            $day = $from->copy()->addDays($i);
            $row = $rows->get($day->toDateString());
            $chart[] = ['label' => $day->format('d M'), 'orders' => (int) ($row->orders ?? 0), 'revenue' => (float) ($row->revenue ?? 0)];
        }

        $topCourses = CourseOrderItem::select('course_id', 'title', DB::raw('COUNT(*) sales'), DB::raw('SUM(course_order_items.price) amount'))
            ->join('course_orders', 'course_orders.id', '=', 'course_order_items.order_id')
            ->where('course_orders.status', '!=', 'cancelled')
            ->groupBy('course_id', 'title')->orderByDesc('sales')->take(5)->get();

        $recentOrders = CourseOrder::withCount('items')->latest()->take(8)->get();

        $methodSplit = CourseOrder::where('status', '!=', 'cancelled')
            ->selectRaw('payment_method, COUNT(*) c, SUM(total) amount')->groupBy('payment_method')->get();

        // Same counters the previous dashboard showed, now based on courses / course orders
        $confirmed = CourseOrder::where('status', 'confirmed');
        $classic = [
            'courses'     => Course::count(),
            'customers'   => CourseOrder::distinct('mobile')->count('mobile'),
            'admins'      => \App\Models\User::where('status', '<>', 2)->where('admin', true)->count(),
            'pages'       => \App\Models\Post::where('type', 0)->where('status', 'active')->count(),
            'sale_today'  => (clone $confirmed)->whereDate('created_at', today())->sum('total'),
            'sale_month'  => (clone $confirmed)->where('created_at', '>=', now()->startOfMonth())->sum('total'),
            'sale_year'   => (clone $confirmed)->where('created_at', '>=', now()->startOfYear())->sum('total'),
            'orders'      => CourseOrder::count(),
            'pending'     => CourseOrder::where('status', 'pending')->count(),
            'confirmed'   => CourseOrder::where('status', 'confirmed')->count(),
            'hold'        => CourseOrder::where('status', 'hold')->count(),
            'cancelled'   => CourseOrder::where('status', 'cancelled')->count(),
        ];

        $latestCourses = Course::with('category:id,name')->withCount('lessons')->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'chart', 'topCourses', 'recentOrders', 'methodSplit', 'classic', 'latestCourses'));
    }

    public function index(Request $r)
    {
        $status = array_key_exists($r->status, CourseOrder::STATUSES) ? $r->status : null;

        $orders = CourseOrder::withCount('items')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($r->payment === 'unverified', fn ($q) => $q->where('payment_status', 'unverified'))
            ->when(in_array($r->method, ['bkash', 'nagad', 'bank']), fn ($q) => $q->where('payment_method', $r->method))
            ->when($r->q, function ($q) use ($r) {
                $s = trim($r->q);
                $q->where(fn ($qq) => $qq->where('invoice', 'like', "%$s%")->orWhere('name', 'like', "%$s%")
                    ->orWhere('mobile', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('transaction_id', 'like', "%$s%"));
            })
            ->when($r->from, fn ($q) => $q->whereDate('created_at', '>=', $r->from))
            ->when($r->to, fn ($q) => $q->whereDate('created_at', '<=', $r->to))
            ->latest()->paginate(20)->withQueryString();

        $counts = CourseOrder::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');
        $counts['all'] = $counts->sum();

        return view('admin.course-orders.index', compact('orders', 'counts', 'status'));
    }

    public function show(CourseOrder $order)
    {
        $order->load('items.course:id,slug,thumbnail,title', 'verifier:id,name');

        $duplicates = CourseOrder::where('id', '!=', $order->id)
            ->where(fn ($q) => $q->when($order->transaction_id !== '', fn ($qq) => $qq->where('transaction_id', $order->transaction_id))->orWhere('mobile', $order->mobile))
            ->latest()->take(5)->get();

        return view('admin.course-orders.show', compact('order', 'duplicates'));
    }

    public function update(Request $r, CourseOrder $order)
    {
        $data = $r->validate([
            'status'         => 'required|in:' . implode(',', array_keys(CourseOrder::STATUSES)),
            'payment_status' => 'required|in:' . implode(',', array_keys(CourseOrder::PAYMENT_STATUSES)),
            'admin_note'     => 'nullable|string|max:2000',
        ]);

        if ($data['payment_status'] === 'verified' && $order->payment_status !== 'verified') {
            $order->verified_by = Auth::id();
            $order->verified_at = now();
        }
        if ($data['payment_status'] !== 'verified') {
            $order->verified_by = null;
            $order->verified_at = null;
        }
        $order->fill($data)->save();

        return back()->with('success', 'Order ' . $order->invoice . ' updated');
    }

    /** One-click actions from the order list / detail page. */
    public function quick(Request $r, CourseOrder $order)
    {
        $action = $r->input('action');
        if ($action === 'confirm') {
            $order->status = 'confirmed';
            $order->payment_status = 'verified';
            $order->verified_by = Auth::id();
            $order->verified_at = now();
        } elseif ($action === 'hold') {
            $order->status = 'hold';
        } elseif ($action === 'cancel') {
            $order->status = 'cancelled';
        } else {
            return back()->with('error', 'Unknown action');
        }
        $order->save();
        return back()->with('success', 'Order ' . $order->invoice . ' ' . ['confirm' => 'confirmed', 'hold' => 'put on hold', 'cancel' => 'cancelled'][$action]);
    }

    public function destroy(CourseOrder $order)
    {
        $order->delete();
        return redirect()->route('admin.course-orders.index')->with('success', 'Order deleted');
    }

    /** Students = unique customers taken from orders (no student accounts). */
    public function students(Request $r)
    {
        $students = CourseOrder::select('mobile', DB::raw('MAX(name) name'), DB::raw('MAX(email) email'),
                DB::raw('COUNT(*) orders'), DB::raw("SUM(CASE WHEN status='confirmed' THEN total ELSE 0 END) spent"),
                DB::raw("SUM(status='confirmed') confirmed"), DB::raw('MAX(created_at) last_order'))
            ->when($r->q, fn ($q) => $q->where(fn ($qq) => $qq->where('name', 'like', "%{$r->q}%")->orWhere('mobile', 'like', "%{$r->q}%")->orWhere('email', 'like', "%{$r->q}%")))
            ->groupBy('mobile')->orderByDesc('last_order')->paginate(25)->withQueryString();

        return view('admin.course-orders.students', compact('students'));
    }

    public function exportStudents()
    {
        $rows = CourseOrder::select('mobile', DB::raw('MAX(name) name'), DB::raw('MAX(email) email'), DB::raw('COUNT(*) orders'),
                DB::raw("SUM(CASE WHEN status='confirmed' THEN total ELSE 0 END) spent"), DB::raw('MAX(created_at) last_order'))
            ->groupBy('mobile')->orderByDesc('last_order')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Mobile', 'Email', 'Orders', 'Confirmed spend (BDT)', 'Last order']);
            foreach ($rows as $s) {
                fputcsv($out, [$s->name, $s->mobile, $s->email, $s->orders, $s->spent, $s->last_order]);
            }
            fclose($out);
        }, 'trainersbd-students-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /* =========================================================
       Manual payment instructions
       ========================================================= */

    public function paymentSettings()
    {
        return view('admin.course-orders.payment-settings', ['methods' => Storefront::allPaymentMethods()]);
    }

    public function paymentSettingsSave(Request $r)
    {
        $r->validate([
            'methods'             => 'required|array',
            'methods.*.key'       => 'required|in:bkash,nagad,bank',
            'methods.*.name'      => 'required|string|max:50',
            'methods.*.type'      => 'nullable|string|max:120',
            'methods.*.account'   => 'required|string|max:80',
            'methods.*.details'   => 'nullable|string|max:200',
            'methods.*.steps'     => 'nullable|string|max:1500',
        ]);

        $methods = collect($r->methods)->map(fn ($m) => [
            'key'     => $m['key'],
            'name'    => $m['name'],
            'enabled' => !empty($m['enabled']),
            'type'    => $m['type'] ?? '',
            'account' => $m['account'],
            'details' => $m['details'] ?? '',
            'steps'   => $m['steps'] ?? '',
        ])->values();

        if (!$methods->contains('enabled', true)) {
            return back()->withInput()->with('error', 'At least one payment method must stay enabled.');
        }

        $g = general();
        $g->payment_methods = $methods->toJson();
        $g->save();

        return back()->with('success', 'Payment settings saved');
    }
}
