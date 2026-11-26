@extends('admin.layouts.app')
@section('title')<title>Customer Report | {{ general()->title }} Admin</title>@endsection
@php use App\Support\Storefront; @endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>Reports</li><li>Customers</li></ul>
        <h1>Customer Report</h1>
        <p>Students who placed course orders, grouped by mobile number.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="{{ route('admin.reportsAll', array_merge(['type' => $type], request()->except('page'), ['export' => 'csv'])) }}" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="{{ route('admin.reportsAll', $type) }}" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

@include('admin.alerts')
@include('admin.reports._range', ['defaultRange' => 'all'])

<div class="adm-stats">
    <div class="adm-stat"><div class="adm-stat__icon"><i class="fa-solid fa-users"></i></div><div class="adm-stat__label">Customers</div><div class="adm-stat__value">{{ number_format($totals['customers']) }}</div><div class="adm-stat__meta">Placed at least one order</div></div>
    <div class="adm-stat adm-stat--ok"><div class="adm-stat__icon"><i class="fa-solid fa-user-graduate"></i></div><div class="adm-stat__label">Paying students</div><div class="adm-stat__value">{{ number_format($totals['buyers']) }}</div><div class="adm-stat__meta">With a confirmed order</div></div>
    <div class="adm-stat adm-stat--hero"><div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div><div class="adm-stat__label">Total spent (confirmed)</div><div class="adm-stat__value">{{ Storefront::money($totals['spent']) }}</div></div>
    <div class="adm-stat adm-stat--warn"><div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div><div class="adm-stat__label">Due (pending / hold)</div><div class="adm-stat__value">{{ Storefront::money($totals['due']) }}</div></div>
</div>

<div class="adm-card">
    <div class="adm-card__head">
        <form method="get" action="{{ route('admin.reportsAll', $type) }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%">
            @foreach(request()->only(['range', 'startDate', 'endDate']) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
            <select name="buyer" class="adm-select adm-select--sm" style="width:190px" onchange="this.form.submit()">
                <option value="">All customers</option>
                <option value="confirmed" @selected(request('buyer') === 'confirmed')>With confirmed order</option>
                <option value="none" @selected(request('buyer') === 'none')>No confirmed order</option>
            </select>
            <select name="sort" class="adm-select adm-select--sm" style="width:170px" onchange="this.form.submit()">
                <option value="">Sort: last order</option>
                <option value="spent" @selected(request('sort') === 'spent')>Sort: spent</option>
                <option value="orders" @selected(request('sort') === 'orders')>Sort: orders</option>
            </select>
            <div class="adm-search" style="width:250px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Name, mobile, email" style="height:36px"></div>
            @if(request()->hasAny(['buyer', 'sort', 'search']))<a href="{{ route('admin.reportsAll', array_merge(['type' => $type], request()->only(['range', 'startDate', 'endDate']))) }}" class="adm-btn adm-btn--sm">Clear</a>@endif
        </form>
    </div>
    @if($customers->isEmpty())
        <div class="adm-empty"><i class="fa-solid fa-users"></i><h5>No customers found</h5><p>Try a wider date range or fewer filters.</p></div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Customer</th><th>Account</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Pending / Hold</th><th class="num">Due</th><th class="num">Spent</th><th>Last order</th></tr></thead>
                <tbody>
                    @foreach($customers as $c)
                        <tr>
                            <td>
                                <strong>{{ $c->name }}</strong>
                                <small class="d-block"><a href="{{ route('admin.reportsAll', ['type' => 'order-reports', 'range' => 'all', 'search' => $c->mobile]) }}" title="View this customer's orders">{{ $c->mobile }}</a></small>
                                <small class="d-block text-muted">{{ $c->email }}</small>
                            </td>
                            <td>@if($c->user_id)<span class="adm-badge adm-badge--success">Student account</span>@else<span class="adm-badge adm-badge--muted">Guest</span>@endif</td>
                            <td class="num">{{ $c->orders }}</td>
                            <td class="num">{{ (int) $c->confirmed }}</td>
                            <td class="num">{{ (int) $c->pending }}</td>
                            <td class="num">{{ Storefront::money($c->due) }}</td>
                            <td class="num"><b>{{ Storefront::money($c->spent) }}</b></td>
                            <td>{{ \Carbon\Carbon::parse($c->last_order)->format('d M Y') }}<small class="d-block text-muted">since {{ \Carbon\Carbon::parse($c->first_order)->format('d M Y') }}</small></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="adm-pager">
            <span>Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers</span>
            {{ $customers->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
