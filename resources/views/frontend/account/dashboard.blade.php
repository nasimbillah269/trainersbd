@extends('frontend.layouts.app')

@php
    use App\Support\Storefront;
    use App\Models\CourseOrder;
    $firstName = \Illuminate\Support\Str::before($user->name, ' ') ?: $user->name;
@endphp

@section('title', 'My Courses | TrainersBD')
@section('body_class', 'page-account')
@push('css')<meta name="robots" content="noindex, nofollow">@endpush

@section('content')
<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Courses</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <h1 class="page-head__title">Hi, {{ $firstName }} 👋</h1>
                <p class="text-muted mb-0">{{ $user->mobile }} @if($user->email)&middot; {{ $user->email }}@endif</p>
            </div>
            <form method="post" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-right"></i> Log out</button>
            </form>
        </div>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-warning"><i class="bi bi-info-circle"></i> {{ session('error') }}</div>
        @endif

        <div class="account-stats">
            <div class="account-stat"><i class="bi bi-mortarboard"></i><div><strong>{{ $stats['enrolled'] }}</strong><span>Enrolled courses</span></div></div>
            <div class="account-stat"><i class="bi bi-hourglass-split"></i><div><strong>{{ $stats['pending'] }}</strong><span>Awaiting verification</span></div></div>
            <div class="account-stat"><i class="bi bi-receipt"></i><div><strong>{{ $stats['orders'] }}</strong><span>Orders</span></div></div>
        </div>

        <h2 class="block-title mt-5 mb-3">My courses</h2>
        @if($courses->isEmpty())
            <div class="empty-state">
                <span class="empty-state__icon"><i class="bi bi-journal-bookmark"></i></span>
                <h2>No courses yet</h2>
                <p>Courses you buy will show up here.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">Browse Courses</a>
            </div>
        @else
            <div class="row g-4">
                @foreach($courses as $row)
                    @php($course = $row['course'])
                    <div class="col-sm-6 col-lg-4">
                        <article class="my-course {{ $row['enrolled'] ? '' : 'is-pending' }}">
                            <a href="{{ $row['enrolled'] ? route('customer.learn', $course->slug) : $course->url() }}" class="course-thumb {{ $course->thumbnailUrl() ? 'course-thumb--img' : '' }}">
                                @if($course->thumbnailUrl())
                                    <img src="{{ $course->thumbnailUrl() }}" alt="" loading="lazy" decoding="async">
                                @else
                                    <i class="bi {{ $course->icon() }} course-thumb__icon"></i>
                                @endif
                            </a>
                            <div class="my-course__body">
                                @if($row['enrolled'])
                                    <span class="my-course__status is-ok"><i class="bi bi-check-circle-fill"></i> Enrolled</span>
                                @else
                                    <span class="my-course__status"><i class="bi bi-hourglass-split"></i> {{ $row['order']->status === 'hold' ? 'On hold' : 'Payment being verified' }}</span>
                                @endif
                                <h3><a href="{{ $row['enrolled'] ? route('customer.learn', $course->slug) : $course->url() }}">{{ $course->title }}</a></h3>
                                <p>{{ $course->instructor->name ?? '' }}</p>
                                <div class="course-meta"><span><i class="bi bi-play-circle"></i> {{ $course->lessonsCount() }} lessons</span>@if($course->duration)<span><i class="bi bi-clock"></i> {{ $course->duration }}</span>@endif</div>
                                @if($row['enrolled'])
                                    <a href="{{ route('customer.learn', $course->slug) }}" class="btn btn-primary w-100 mt-3"><i class="bi bi-play-fill"></i> Start learning</a>
                                @else
                                    <p class="my-course__note">You'll get access as soon as we verify your payment for order #{{ $row['order']->invoice }}.</p>
                                @endif
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="row g-4 mt-4">
            <div class="col-lg-8">
                <div class="panel h-100">
                    <h2 class="panel__title mb-3">Order history</h2>
                    @if($orders->isEmpty())
                        <p class="text-muted mb-0">You haven't placed any orders yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle account-orders mb-0">
                                <thead><tr><th>Order</th><th>Courses</th><th>Total</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td><strong>#{{ $order->invoice }}</strong><small class="d-block text-muted">{{ $order->created_at->format('d M Y') }}</small></td>
                                            <td>{{ $order->items->pluck('title')->implode(', ') }}</td>
                                            <td class="text-nowrap">{{ Storefront::money($order->total) }}<small class="d-block text-muted">{{ $order->methodName() }}</small></td>
                                            <td><span class="order-status order-status--{{ $order->status }}">{{ CourseOrder::STATUSES[$order->status] ?? ucfirst($order->status) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel h-100">
                    <h2 class="panel__title mb-3">Change password</h2>
                    <form method="post" action="{{ route('customer.password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="pwCurrent">Current password</label>
                            <input type="password" class="form-control @error('current_password', 'password') is-invalid @enderror" id="pwCurrent" name="current_password" required autocomplete="current-password">
                            @error('current_password', 'password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pwNew">New password</label>
                            <input type="password" class="form-control @error('password', 'password') is-invalid @enderror" id="pwNew" name="password" required minlength="6" autocomplete="new-password">
                            @error('password', 'password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pwConfirm">Confirm new password</label>
                            <input type="password" class="form-control" id="pwConfirm" name="password_confirmation" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-soft w-100">Update password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
