<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\CourseReview;
use App\Models\User;
use App\Support\Storefront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function home()
    {
        $slider = Attribute::where('type', 1)->where('location', 'Front Page Slider')
            ->whereNull('parent_id')->where('status', 'active')->first();

        $slides = $slider
            ? $slider->subSliders()->with('imageFile')->get()->filter(fn ($s) => $s->imageFile)->values()
            : collect();

        $courses = Course::active()->forCards()->where('type', 'paid')
            ->orderByDesc('featured')->orderBy('sort')->get();

        $freeCourses = Course::active()->forCards()->where('type', 'free')
            ->orderBy('sort')->get();

        $stats = [
            'students'    => Course::active()->sum('students_count'),
            'courses'     => Course::active()->count(),
            'instructors' => \App\Models\Instructor::where('status', true)->count(),
            'rating'      => round((float) \App\Models\CourseReview::where('status', 'approved')->avg('rating'), 1),
        ];

        $testimonials = CourseReview::with('course:id,title')->where('status', 'approved')
            ->where('rating', 5)->latest()->take(3)->get();

        return view('frontend.home', compact('slides', 'courses', 'freeCourses', 'stats', 'testimonials'));
    }

    public function course(string $slug)
    {
        $course = Course::active()->forCards()
            ->with(['instructor', 'modules.lessons', 'approvedReviews' => fn ($q) => $q->latest()])
            ->where('slug', $slug)->firstOrFail();

        $breakdown = [];
        $total = max(1, $course->approvedReviews->count());
        foreach ([5, 4, 3, 2, 1] as $star) {
            $breakdown[$star] = (int) round($course->approvedReviews->where('rating', $star)->count() / $total * 100);
        }

        $related = Course::active()->forCards()
            ->where('id', '!=', $course->id)
            ->orderByRaw('category_id = ? desc', [$course->category_id])
            ->orderByDesc('featured')->take(4)->get();

        return view('frontend.course', compact('course', 'breakdown', 'related'));
    }

    public function review(Request $r, string $slug)
    {
        $course = Course::active()->where('slug', $slug)->firstOrFail();

        $data = $r->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|max:150',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:5|max:2000',
        ]);

        $course->reviews()->create($data + ['status' => 'pending']);

        return response()->json(['success' => true, 'message' => 'Thank you! Your review will appear after approval.']);
    }

    public function cart()
    {
        $suggested = Course::active()->forCards()->where('type', 'paid')
            ->orderByDesc('featured')->orderBy('sort')->take(4)->get();

        return view('frontend.cart', compact('suggested'));
    }

    /** Re-price the browser cart from the database (prices are never trusted from the client). */
    public function cartSync(Request $r)
    {
        $ids = collect($r->input('ids', []))->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id)->unique()->take(50);

        $courses = Course::active()->forCards()->where('type', 'paid')->whereIn('id', $ids)->get()
            ->sortBy(fn ($c) => $ids->search($c->id))->values();

        return response()->json(['items' => $courses->map->cartData()]);
    }

    public function checkout()
    {
        return view('frontend.checkout', ['paymentMethods' => Storefront::paymentMethods(), 'customer' => $this->checkoutCustomer()]);
    }

    public function placeOrder(Request $r)
    {
        $methods = collect(Storefront::paymentMethods())->pluck('key')->all();

        $v = Validator::make($r->all(), [
            'name'           => 'required|string|max:100',
            'mobile'         => ['required', 'regex:/^(\+?88)?01[3-9][0-9]{8}$/'],
            'email'          => 'required|email|max:150',
            'payment_method' => 'required|in:' . implode(',', $methods),
            'sender_number'  => (config('trainersbd.payment_fields_required') ? 'required' : 'nullable') . '|string|max:60',
            'transaction_id' => config('trainersbd.payment_fields_required') ? 'required|string|min:6|max:80' : 'nullable|string|max:80',
            'note'           => 'nullable|string|max:500',
            'items'          => 'required|array|min:1|max:50',
            'items.*'        => 'integer',
            'password'       => $this->checkoutCustomer() ? 'nullable' : 'required|string|min:6|max:100',
        ], [
            'mobile.regex' => 'Please enter a valid Bangladeshi mobile number.',
            'items.required' => 'Your cart is empty.',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $courses = Course::active()->where('type', 'paid')->whereIn('id', $r->items)->get();
        if ($courses->isEmpty()) {
            return response()->json(['success' => false, 'errors' => ['items' => ['These courses are no longer available.']]], 422);
        }

        // Guests get a student account so they can see their courses later; it must not already exist
        $customer = $this->checkoutCustomer();
        $mobile = AccountController::normalizeMobile($r->mobile);
        $email = strtolower(trim($r->email));
        if (!$customer) {
            $taken = [];
            if (User::where('mobile', $mobile)->exists()) {
                $taken['mobile'] = ['An account with this mobile number already exists. Please log in first.'];
            }
            if (User::where('email', $email)->exists()) {
                $taken['email'] = ['An account with this email already exists. Please log in first.'];
            }
            if ($taken) {
                return response()->json(['success' => false, 'login' => route('customer.login'), 'errors' => $taken], 422);
            }
        }

        $trx = strtoupper(trim((string) $r->transaction_id));
        if ($trx !== '' && CourseOrder::where('transaction_id', $trx)->where('status', '!=', 'cancelled')->exists()) {
            return response()->json(['success' => false, 'errors' => ['transaction_id' => ['This transaction ID has already been used for another order.']]], 422);
        }

        $order = DB::transaction(function () use ($r, $courses, $trx, &$customer, $mobile, $email) {
            if (!$customer) {
                $customer = User::create([
                    'name'     => trim($r->name),
                    'email'    => $email,
                    'password' => Hash::make($r->password),
                ]);
                $customer->forceFill(['mobile' => $mobile, 'customer' => true, 'admin' => false, 'status' => 1, 'country' => 1])->save();
            }

            $subtotal = $courses->sum(fn ($c) => max($c->old_price, $c->price));
            $total = $courses->sum('price');

            $order = CourseOrder::create([
                'invoice'        => 'TMP' . uniqid(),
                'user_id'        => $customer->id,
                'name'           => trim($r->name),
                'mobile'         => trim($r->mobile),
                'email'          => strtolower(trim($r->email)),
                'payment_method' => $r->payment_method,
                'sender_number'  => trim((string) $r->sender_number),
                'transaction_id' => $trx,
                'subtotal'       => $subtotal,
                'discount'       => $subtotal - $total,
                'total'          => $total,
                'note'           => $r->note,
                'ip'             => $r->ip(),
            ]);
            $order->invoice = 'TBD' . now()->format('ymd') . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            foreach ($courses as $c) {
                $order->items()->create(['course_id' => $c->id, 'title' => $c->title, 'price' => $c->price, 'old_price' => $c->old_price]);
            }
            return $order;
        });

        if (!Auth::check()) {
            Auth::login($customer);
        }

        return response()->json([
            'success'  => true,
            'redirect' => \Illuminate\Support\Facades\URL::signedRoute('order.success', ['invoice' => $order->invoice]),
            'order'    => [
                'invoice' => $order->invoice,
                'name'    => $order->name,
                'mobile'  => $order->mobile,
                'total'   => Storefront::money($order->total),
                'method'  => $order->methodName(),
                'trx'     => $order->transaction_id,
            ],
        ]);
    }

    /** The logged-in student placing an order (admins ordering for testing are treated as guests). */
    protected function checkoutCustomer(): ?User
    {
        $user = Auth::user();
        return $user && !$user->admin ? $user : null;
    }

    /** Thank-you page after checkout (signed URL, so only the buyer has the link). */
    public function orderSuccess(string $invoice)
    {
        $order = CourseOrder::with('items.course:id,slug,title,thumbnail,category_id', 'items.course.category:id,name,icon')
            ->where('invoice', $invoice)->firstOrFail();

        $method = collect(Storefront::allPaymentMethods())->firstWhere('key', $order->payment_method);

        return view('frontend.order-success', compact('order', 'method'));
    }

    /* =========================================================
       Courses listing  (/courses, /courses/category/{slug}, /free-courses)
       ========================================================= */

    public function courses(Request $r)
    {
        return $this->listing($r);
    }

    public function coursesCategory(Request $r, string $slug)
    {
        $category = \App\Models\CourseCategory::where('slug', $slug)->where('status', true)->firstOrFail();
        return $this->listing($r, ['category' => $category]);
    }

    public function freeCourses(Request $r)
    {
        return $this->listing($r, ['type' => 'free']);
    }

    protected function listing(Request $r, array $preset = [], $page = null)
    {
        $category = $preset['category'] ?? null;
        if (!$category && $r->filled('category')) {
            $category = \App\Models\CourseCategory::where('slug', $r->category)->where('status', true)->first();
        }
        $type   = $preset['type'] ?? (in_array($r->type, ['paid', 'free']) ? $r->type : null);
        $levels = array_values(array_filter((array) $r->input('level', []), 'is_string'));
        $rating = in_array((int) $r->rating, [3, 4]) ? (int) $r->rating : null;
        $sort   = in_array($r->sort, ['popular', 'newest', 'rating', 'price-asc', 'price-desc']) ? $r->sort : 'popular';
        $q      = trim((string) $r->q);

        $query = Course::active()->forCards()
            ->when($category, fn ($qq) => $qq->where('category_id', $category->id))
            ->when($type, fn ($qq) => $qq->where('type', $type))
            ->when($levels, fn ($qq) => $qq->whereIn('level', $levels))
            ->when($q !== '', fn ($qq) => $qq->where(fn ($w) => $w->where('title', 'like', "%$q%")->orWhere('subtitle', 'like', "%$q%")))
            ->when($rating, fn ($qq) => $qq->whereHas('approvedReviews')->having('rating_avg', '>=', $rating));

        match ($sort) {
            'newest'     => $query->latest('id'),
            'rating'     => $query->orderByDesc('rating_avg'),
            'price-asc'  => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            default      => $query->orderByRaw("type = 'paid' desc")->orderByDesc('featured')->orderByDesc('students_count'),
        };

        // HAVING needs the aggregate in the same query, so paginate on the collection when filtering by rating
        if ($rating) {
            $all = $query->get();
            $courses = new \Illuminate\Pagination\LengthAwarePaginator(
                $all->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 12)->values(), $all->count(), 12, null,
                ['path' => $r->url(), 'query' => $r->query()]
            );
        } else {
            $courses = $query->paginate(12)->withQueryString();
        }

        $base = Course::active();
        $facets = [
            'types'  => (clone $base)->when($category, fn ($qq) => $qq->where('category_id', $category->id))
                            ->selectRaw('type, COUNT(*) c')->groupBy('type')->pluck('c', 'type'),
            'levels' => (clone $base)->whereNotNull('level')->where('level', '!=', '')
                            ->when($category, fn ($qq) => $qq->where('category_id', $category->id))
                            ->when($type, fn ($qq) => $qq->where('type', $type))
                            ->selectRaw('level, COUNT(*) c')->groupBy('level')->orderBy('level')->pluck('c', 'level'),
            'total'  => (clone $base)->count(),
        ];

        if ($page) {
            $title = $page->name;
            $intro = strip_tags((string) $page->short_description) ?: null;
        } elseif ($category) {
            $title = $category->name . ' Courses';
            $intro = $category->description ?: 'Practical ' . $category->name . ' courses taught in Bangla by industry professionals.';
        } elseif ($type === 'free') {
            $title = 'Free Courses';
            $intro = 'Complete beginner courses you can watch right here, with no payment or sign-up.';
        } else {
            $title = 'All Courses';
            $intro = 'Browse every TrainersBD course and find the right skill for your next step.';
        }

        // Base URL that filters submit to (keeps pretty URLs for category / free pages)
        $action = $page ? route('pageView', $page->slug)
            : ($category && isset($preset['category']) ? route('courses.category', $category->slug)
            : ($type === 'free' && isset($preset['type']) ? route('courses.free') : route('courses.index')));

        return view('frontend.courses', compact('courses', 'category', 'type', 'levels', 'rating', 'sort', 'q', 'facets', 'title', 'intro', 'action', 'preset', 'page'));
    }

    /* =========================================================
       CMS pages from Admin → Pages  (/{slug})
       ========================================================= */

    public function page(Request $r, string $slug)
    {
        $page = \App\Models\Post::where('type', 0)->where('status', 'active')->where('slug', $slug)->firstOrFail();

        return match ($page->template) {
            'Front Page'   => redirect()->route('index'),
            'All Courses'  => $this->listing($r, [], $page),
            'Free Courses' => $this->listing($r, ['type' => 'free'], $page),
            default        => view('frontend.page', compact('page')),
        };
    }

    /** Old storefront URLs that the admin panel still links to. */
    public function legacyProduct(string $slug)
    {
        return redirect()->route('course.show', $slug);
    }

    public function legacyHome()
    {
        return redirect()->route('index');
    }
}
