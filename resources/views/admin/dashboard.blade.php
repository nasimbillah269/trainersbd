@extends('admin.layouts.app')
@section('title')<title>Dashboard | {{ general()->title }} Admin</title>@endsection

@php
    use App\Support\Storefront;
    $maxRev = max(1, collect($chart)->max('revenue'));
    $maxOrd = max(1, collect($chart)->max('orders'));
    $hour = now()->hour;
    $greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <h1>{{ $greet }}, {{ \Illuminate\Support\Str::before(Auth::user()->name, ' ') ?: 'Admin' }} 👋</h1>
        <p>Here's what's happening with {{ general()->title }} today, {{ now()->format('l, d F Y') }}.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="{{ route('admin.course-orders.index', ['payment' => 'unverified']) }}" class="adm-btn"><i class="fa-solid fa-shield-halved"></i> Verify payments</a>
        <a href="{{ route('admin.courses.create') }}" class="adm-btn adm-btn--primary"><i class="fa-solid fa-plus"></i> Add Course</a>
    </div>
</div>

@if($stats['unverified'])
    <div class="adm-alert adm-alert--warning">
        <i class="fa-solid fa-hourglass-half"></i>
        <div><strong>{{ $stats['unverified'] }} {{ \Illuminate\Support\Str::plural('payment', $stats['unverified']) }} waiting for verification.</strong> Check the transaction IDs in your bKash / Nagad / bank statement and confirm the orders.
            <a href="{{ route('admin.course-orders.index', ['payment' => 'unverified']) }}" class="ml-1"><b>Review now &rarr;</b></a></div>
    </div>
@endif

{{-- Counters from the previous dashboard --}}
<div class="adm-mini-stats">
    <a href="{{ route('admin.courses.index') }}" class="adm-mini"><span class="adm-mini__icon"><i class="fa-solid fa-layer-group"></i></span><div><strong>{{ number_format($classic['courses']) }}</strong><small>Courses</small></div></a>
    <a href="{{ route('admin.students.index') }}" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-users"></i></span><div><strong>{{ number_format($classic['customers']) }}</strong><small>Customers</small></div></a>
    <a href="{{ route('admin.usersAdmin') }}" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-user-shield"></i></span><div><strong>{{ number_format($classic['admins']) }}</strong><small>Admin Users</small></div></a>
    <a href="{{ route('admin.pages') }}" class="adm-mini" style="--mi:#0086C9;--mi-bg:#F0F9FF"><span class="adm-mini__icon"><i class="fa-regular fa-file-lines"></i></span><div><strong>{{ number_format($classic['pages']) }}</strong><small>Pages</small></div></a>

    <div class="adm-mini" style="--mi:#003080"><span class="adm-mini__icon"><i class="fa-solid fa-calendar-day"></i></span><div><strong>{{ Storefront::money($classic['sale_today']) }}</strong><small>Today's sales</small></div></div>
    <div class="adm-mini" style="--mi:#0d47b8;--mi-bg:#F4F3FF"><span class="adm-mini__icon"><i class="fa-solid fa-calendar-week"></i></span><div><strong>{{ Storefront::money($classic['sale_month']) }}</strong><small>Monthly sales</small></div></div>
    <div class="adm-mini" style="--mi:#C11574;--mi-bg:#FDF2F8"><span class="adm-mini__icon"><i class="fa-solid fa-calendar"></i></span><div><strong>{{ Storefront::money($classic['sale_year']) }}</strong><small>Yearly sales</small></div></div>
    <a href="{{ route('admin.course-orders.index') }}" class="adm-mini" style="--mi:#1E293B;--mi-bg:#F1F3F8"><span class="adm-mini__icon"><i class="fa-solid fa-receipt"></i></span><div><strong>{{ number_format($classic['orders']) }}</strong><small>Orders</small></div></a>

    <a href="{{ route('admin.course-orders.index', ['status' => 'pending']) }}" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-clock"></i></span><div><strong>{{ number_format($classic['pending']) }}</strong><small>Pending Orders</small></div></a>
    <a href="{{ route('admin.course-orders.index', ['status' => 'confirmed']) }}" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-circle-check"></i></span><div><strong>{{ number_format($classic['confirmed']) }}</strong><small>Confirmed Orders</small></div></a>
    <a href="{{ route('admin.course-orders.index', ['status' => 'hold']) }}" class="adm-mini" style="--mi:#B54708;--mi-bg:#FEF0C7"><span class="adm-mini__icon"><i class="fa-solid fa-pause"></i></span><div><strong>{{ number_format($classic['hold']) }}</strong><small>On Hold Orders</small></div></a>
    <a href="{{ route('admin.course-orders.index', ['status' => 'cancelled']) }}" class="adm-mini" style="--mi:#B42318;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-solid fa-ban"></i></span><div><strong>{{ number_format($classic['cancelled']) }}</strong><small>Cancelled Orders</small></div></a>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card__head">
                <div>
                    <h3 class="adm-card__title"><i class="fa-solid fa-chart-column"></i> Sales overview</h3>
                    <p class="adm-card__sub">Confirmed revenue and orders, last 14 days</p>
                </div>
                <div class="d-flex gap-3" style="gap:1.25rem">
                    <div><small class="text-muted d-block">Revenue</small><strong class="text-dark">{{ Storefront::money(collect($chart)->sum('revenue')) }}</strong></div>
                    <div><small class="text-muted d-block">Orders</small><strong class="text-dark">{{ collect($chart)->sum('orders') }}</strong></div>
                </div>
            </div>
            <div class="adm-card__body">
                <div class="adm-chart">
                    @foreach($chart as $day)
                        @php($h = $day['revenue'] > 0 ? max(4, $day['revenue'] / $maxRev * 100) : ($day['orders'] > 0 ? max(4, $day['orders'] / $maxOrd * 30) : 3))
                        <div class="adm-chart__col">
                            <div class="adm-chart__bars">
                                <div class="adm-chart__bar {{ $day['revenue'] > 0 ? '' : 'adm-chart__bar--empty' }}" style="height: {{ $h }}%"></div>
                                <span class="adm-chart__tip">{{ $day['label'] }}: {{ Storefront::money($day['revenue']) }} &middot; {{ $day['orders'] }} orders</span>
                            </div>
                            <span class="adm-chart__label">{{ \Illuminate\Support\Str::before($day['label'], ' ') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head">
                <h3 class="adm-card__title"><i class="fa-solid fa-clock-rotate-left"></i> Recent orders</h3>
                <a href="{{ route('admin.course-orders.index') }}" class="adm-btn adm-btn--sm">View all</a>
            </div>
            @if($recentOrders->isEmpty())
                <div class="adm-empty"><i class="fa-solid fa-receipt"></i><h5>No orders yet</h5><p>New orders from the checkout page will appear here.</p></div>
            @else
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>Order</th><th>Student</th><th>Payment</th><th>Status</th><th class="num">Total</th></tr></thead>
                        <tbody>
                            @foreach($recentOrders as $o)
                                <tr>
                                    <td><a href="{{ route('admin.course-orders.show', $o) }}" class="adm-mono"><b>#{{ $o->invoice }}</b></a><small class="d-block text-muted">{{ $o->created_at->diffForHumans() }}</small></td>
                                    <td><div class="adm-cell"><div><strong>{{ $o->name }}</strong><small>{{ $o->mobile }}</small></div></div></td>
                                    <td><span class="adm-badge adm-badge--plain adm-badge--{{ $o->payment_method }}">{{ $o->methodName() }}</span><small class="d-block text-muted adm-mono mt-1">{{ $o->transaction_id }}</small></td>
                                    <td><span class="adm-badge adm-badge--{{ $o->status }}">{{ \App\Models\CourseOrder::STATUSES[$o->status] }}</span></td>
                                    <td class="num"><b>{{ Storefront::money($o->total) }}</b><small class="d-block text-muted">{{ $o->items_count }} {{ \Illuminate\Support\Str::plural('course', $o->items_count) }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="col-xl-4">
        <div class="adm-card">
            <div class="adm-card__head">
                <h3 class="adm-card__title"><i class="fa-solid fa-layer-group"></i> Latest Courses</h3>
                <a href="{{ route('admin.courses.index') }}" class="adm-btn adm-btn--sm">All</a>
            </div>
            <ul class="adm-list">
                @forelse($latestCourses as $lc)
                    <li>
                        @if($lc->thumbnailUrl())<img src="{{ $lc->thumbnailUrl() }}" class="adm-thumb" style="width:64px" alt="">@else<span class="adm-thumb" style="width:64px"><i class="fa-regular fa-image"></i></span>@endif
                        <div class="adm-list__main">
                            <a href="{{ route('admin.courses.edit', $lc) }}"><strong>{{ $lc->title }}</strong></a>
                            <small>{{ $lc->isFree() ? 'Free' : Storefront::money($lc->price) }} &middot; {{ $lc->lessons_count }} lessons &middot; <span class="adm-badge adm-badge--{{ $lc->status }}" style="font-size:.66rem;padding:.05rem .45rem">{{ $lc->status === 'active' ? 'Published' : 'Draft' }}</span></small>
                        </div>
                    </li>
                @empty
                    <li><small class="text-muted">No courses yet.</small></li>
                @endforelse
            </ul>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-trophy"></i> Top selling courses</h3></div>
            @if($topCourses->isEmpty())
                <div class="adm-empty"><i class="fa-solid fa-chart-simple"></i><p class="mb-0">No sales yet.</p></div>
            @else
                @php($maxSales = max(1, $topCourses->max('sales')))
                <ul class="adm-list">
                    @foreach($topCourses as $i => $tc)
                        <li>
                            <span class="adm-rank">{{ $i + 1 }}</span>
                            <div class="adm-list__main">
                                <strong>{{ $tc->title }}</strong>
                                <small>{{ $tc->sales }} sold &middot; {{ Storefront::money($tc->amount) }}</small>
                                <div class="adm-progress"><span style="width: {{ $tc->sales / $maxSales * 100 }}%"></span></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-wallet"></i> Payment methods</h3></div>
            <div class="adm-card__body">
                @php($totalM = max(1, $methodSplit->sum('c')))
                @forelse($methodSplit as $m)
                    @php($label = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'Bank Transfer'][$m->payment_method] ?? $m->payment_method)
                    @php($color = ['bkash' => '#E2136E', 'nagad' => '#F15A22', 'bank' => '#1E40AF'][$m->payment_method] ?? '#003080')
                    <div class="mb-3">
                        <div class="d-flex justify-content-between"><strong class="text-dark" style="font-size:.88rem">{{ $label }}</strong><small class="text-muted">{{ $m->c }} orders &middot; {{ Storefront::money($m->amount) }}</small></div>
                        <div class="adm-progress"><span style="width: {{ $m->c / $totalM * 100 }}%; background: {{ $color }}"></span></div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No payments yet.</p>
                @endforelse
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-bolt"></i> Quick actions</h3></div>
            <div class="adm-card__body d-grid" style="display:grid; gap:.6rem">
                <a href="{{ route('admin.courses.create') }}" class="adm-btn adm-btn--block"><i class="fa-solid fa-plus"></i> Add a new course</a>
                <a href="{{ route('admin.course-reviews.index', ['status' => 'pending']) }}" class="adm-btn adm-btn--block"><i class="fa-regular fa-star"></i> Moderate reviews @if($stats['reviews_pending'])<span class="adm-badge adm-badge--pending ml-1">{{ $stats['reviews_pending'] }}</span>@endif</a>
                <a href="{{ route('admin.payment-settings') }}" class="adm-btn adm-btn--block"><i class="fa-solid fa-wallet"></i> Update payment numbers</a>
                <a href="{{ route('admin.sliders') }}" class="adm-btn adm-btn--block"><i class="fa-regular fa-images"></i> Edit home slider</a>
            </div>
        </div>
    </div>
</div>
@endsection
