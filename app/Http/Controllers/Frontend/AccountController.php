<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Student account: login, "My Courses" dashboard and the lesson page for purchased courses.
 * Accounts are created at checkout (see SiteController::placeOrder).
 */
class AccountController extends Controller
{
    /** 01XXXXXXXXX form of a Bangladeshi mobile number (accepts +88 / 88 prefixes). */
    public static function normalizeMobile(?string $mobile): string
    {
        $digits = preg_replace('/\D/', '', (string) $mobile);
        return preg_replace('/^88(?=01)/', '', $digits);
    }

    public function login(Request $r)
    {
        if (Auth::check()) {
            return redirect()->route('customer.dashboard');
        }

        if ($r->isMethod('get')) {
            if ($r->query('redirect') === 'checkout') {
                $r->session()->put('url.intended', route('checkout'));
            }
            return view('frontend.account.login');
        }

        $r->validate([
            'login'    => 'required|string|max:150',
            'password' => 'required|string|max:100',
        ], [], ['login' => 'mobile number or email']);

        $login = trim($r->login);
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $value = $field === 'email' ? strtolower($login) : self::normalizeMobile($login);

        $user = User::where('admin', false)->where('customer', true)->where($field, $value)->first();

        if (!$user || !Hash::check($r->password, $user->password)) {
            return back()->withInput($r->only('login'))->withErrors(['login' => 'The mobile number / email or password is incorrect.']);
        }
        if ((int) $user->status === 0) {
            return back()->withInput($r->only('login'))->withErrors(['login' => 'Your account is disabled. Please contact us.']);
        }

        Auth::login($user, $r->boolean('remember'));
        $r->session()->regenerate();

        return redirect()->intended(route('customer.dashboard'));
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('index');
    }

    public function dashboard()
    {
        $user = Auth::user();
        if ($user->admin) {
            return redirect()->route('admin.dashboard');
        }

        $orders = CourseOrder::with(['items.course' => fn ($q) => $q->forCards()])
            ->where('user_id', $user->id)->latest()->get();

        // One card per course: "enrolled" if any confirmed order contains it, otherwise its latest order's state
        $courses = collect();
        foreach ($orders->sortBy(fn ($o) => $o->status === 'confirmed' ? 0 : 1) as $order) {
            foreach ($order->items as $item) {
                if (!$item->course || $courses->has($item->course_id) || $order->status === 'cancelled') {
                    continue;
                }
                $courses->put($item->course_id, ['course' => $item->course, 'order' => $order, 'enrolled' => $order->status === 'confirmed']);
            }
        }
        $courses = $courses->sortByDesc('enrolled')->values();

        $stats = [
            'enrolled' => $courses->where('enrolled', true)->count(),
            'pending'  => $courses->where('enrolled', false)->count(),
            'orders'   => $orders->count(),
        ];

        return view('frontend.account.dashboard', compact('user', 'orders', 'courses', 'stats'));
    }

    /** Full lesson list of a purchased course (only after the order is confirmed). */
    public function learn(string $slug)
    {
        $user = Auth::user();

        $course = Course::where('slug', $slug)->with(['instructor', 'modules.lessons'])->firstOrFail();

        $enrolled = CourseOrder::where('user_id', $user->id)->where('status', 'confirmed')
            ->whereHas('items', fn ($q) => $q->where('course_id', $course->id))->exists();

        if (!$enrolled) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'You can open "' . $course->title . '" once your payment has been verified.');
        }

        return view('frontend.account.learn', compact('course'));
    }

    public function password(Request $r)
    {
        $user = Auth::user();

        $r->validateWithBag('password', [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|max:100|confirmed',
        ]);

        if (!Hash::check($r->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.'], 'password');
        }

        $user->password = Hash::make($r->password);
        $user->save();

        return back()->with('success', 'Your password has been changed.');
    }
}
