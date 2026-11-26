@extends('admin.layouts.app')
@section('title')<title>Course Report | {{ general()->title }} Admin</title>@endsection
@php use App\Support\Storefront; @endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>Reports</li><li>Courses</li></ul>
        <h1>Course Report</h1>
        <p>Orders and revenue per course. Revenue counts confirmed orders only.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="{{ route('admin.reportsAll', array_merge(['type' => $type], request()->except('page'), ['export' => 'csv'])) }}" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="{{ route('admin.reportsAll', $type) }}" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

@include('admin.alerts')
@include('admin.reports._range', ['defaultRange' => 'all'])

<div class="adm-stats">
    <div class="adm-stat adm-stat--hero"><div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div><div class="adm-stat__label">Revenue</div><div class="adm-stat__value">{{ Storefront::money($courses->sum('r_revenue')) }}</div></div>
    <div class="adm-stat adm-stat--warn"><div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div><div class="adm-stat__label">Due (pending / hold)</div><div class="adm-stat__value">{{ Storefront::money($courses->sum('r_due')) }}</div></div>
    <div class="adm-stat adm-stat--info"><div class="adm-stat__icon"><i class="fa-solid fa-cart-shopping"></i></div><div class="adm-stat__label">Course enrolments ordered</div><div class="adm-stat__value">{{ number_format($courses->sum('r_orders')) }}</div><div class="adm-stat__meta">{{ number_format($courses->sum('r_confirmed')) }} confirmed</div></div>
    <div class="adm-stat adm-stat--ok"><div class="adm-stat__icon"><i class="fa-solid fa-layer-group"></i></div><div class="adm-stat__label">Courses with sales</div><div class="adm-stat__value">{{ $courses->where('r_orders', '>', 0)->count() }} / {{ $courses->count() }}</div></div>
</div>

<div class="adm-card">
    <div class="adm-card__head">
        <form method="get" action="{{ route('admin.reportsAll', $type) }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%">
            @foreach(request()->only(['range', 'startDate', 'endDate']) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
            <select name="category" class="adm-select adm-select--sm" style="width:190px" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>@endforeach
            </select>
            <select name="course_type" class="adm-select adm-select--sm" style="width:130px" onchange="this.form.submit()">
                <option value="">Paid &amp; free</option>
                <option value="paid" @selected(request('course_type') === 'paid')>Paid</option>
                <option value="free" @selected(request('course_type') === 'free')>Free</option>
            </select>
            <select name="status" class="adm-select adm-select--sm" style="width:130px" onchange="this.form.submit()">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
            </select>
            <select name="sort" class="adm-select adm-select--sm" style="width:160px" onchange="this.form.submit()">
                <option value="">Sort: revenue</option>
                <option value="orders" @selected(request('sort') === 'orders')>Sort: orders</option>
                <option value="title" @selected(request('sort') === 'title')>Sort: title</option>
            </select>
            <div class="adm-search" style="width:220px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Course title" style="height:36px"></div>
            @if(request()->hasAny(['category', 'course_type', 'status', 'sort', 'search']))<a href="{{ route('admin.reportsAll', array_merge(['type' => $type], request()->only(['range', 'startDate', 'endDate']))) }}" class="adm-btn adm-btn--sm">Clear</a>@endif
        </form>
    </div>
    @if($courses->isEmpty())
        <div class="adm-empty"><i class="fa-solid fa-layer-group"></i><h5>No courses found</h5><p>Try changing the filters.</p></div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>#</th><th>Course</th><th class="num">Price</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Pending / Hold</th><th class="num">Cancelled</th><th class="num">Due</th><th class="num">Revenue</th></tr></thead>
                <tbody>
                    @foreach($courses as $i => $c)
                        <tr style="{{ $c->r_orders || $c->r_cancelled ? '' : 'opacity:.6' }}">
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.courses.edit', $c->id) }}"><strong>{{ $c->title }}</strong></a>
                                <small class="d-block text-muted">{{ $c->category->name ?? 'Uncategorized' }} &middot; {{ ucfirst($c->type) }}@if($c->status !== 'active') &middot; <span class="adm-badge adm-badge--draft">Draft</span>@endif</small>
                            </td>
                            <td class="num">{{ $c->type === 'free' ? 'Free' : Storefront::money($c->price) }}</td>
                            <td class="num">{{ (int) $c->r_orders }}</td>
                            <td class="num">{{ (int) $c->r_confirmed }}</td>
                            <td class="num">{{ (int) $c->r_pending }}</td>
                            <td class="num">{{ (int) $c->r_cancelled }}</td>
                            <td class="num">{{ Storefront::money($c->r_due) }}</td>
                            <td class="num"><b>{{ Storefront::money($c->r_revenue) }}</b></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><th></th><th>Total</th><th></th><th class="num">{{ (int) $courses->sum('r_orders') }}</th><th class="num">{{ (int) $courses->sum('r_confirmed') }}</th><th class="num">{{ (int) $courses->sum('r_pending') }}</th><th class="num">{{ (int) $courses->sum('r_cancelled') }}</th><th class="num">{{ Storefront::money($courses->sum('r_due')) }}</th><th class="num">{{ Storefront::money($courses->sum('r_revenue')) }}</th></tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
@endsection
