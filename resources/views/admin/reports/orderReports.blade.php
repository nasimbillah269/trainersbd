@extends('admin.layouts.app')
@section('title')<title>Order Report | {{ general()->title }} Admin</title>@endsection
@php use App\Support\Storefront; use App\Models\CourseOrder; @endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>Reports</li><li>Orders</li></ul>
        <h1>Order Report</h1>
        <p>All course orders with payment details. Filter, then export to CSV.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="{{ route('admin.reportsAll', array_merge(['type' => $type], request()->except('page'), ['export' => 'csv'])) }}" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="{{ route('admin.reportsAll', $type) }}" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

@include('admin.alerts')
@include('admin.reports._range', ['defaultRange' => 'all'])

<div class="adm-stats">
    <div class="adm-stat"><div class="adm-stat__icon"><i class="fa-solid fa-receipt"></i></div><div class="adm-stat__label">Orders</div><div class="adm-stat__value">{{ number_format($totals->orders) }}</div><div class="adm-stat__meta">Total {{ Storefront::money($totals->amount) }} &middot; discount {{ Storefront::money($totals->discount) }}</div></div>
    <div class="adm-stat adm-stat--hero"><div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div><div class="adm-stat__label">Revenue (confirmed)</div><div class="adm-stat__value">{{ Storefront::money($totals->revenue) }}</div></div>
    <div class="adm-stat adm-stat--warn"><div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div><div class="adm-stat__label">Due (pending / hold)</div><div class="adm-stat__value">{{ Storefront::money($totals->due) }}</div></div>
    <div class="adm-stat adm-stat--info"><div class="adm-stat__icon"><i class="fa-solid fa-calculator"></i></div><div class="adm-stat__label">Average order</div><div class="adm-stat__value">{{ Storefront::money($totals->orders ? $totals->amount / $totals->orders : 0) }}</div></div>
</div>

<div class="adm-card">
    <div class="adm-card__head">
        <form method="get" action="{{ route('admin.reportsAll', $type) }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%">
            @foreach(request()->only(['range', 'startDate', 'endDate']) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
            <select name="status" class="adm-select adm-select--sm" style="width:150px" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach(CourseOrder::STATUSES as $k => $label)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $label }}</option>@endforeach
            </select>
            <select name="payment" class="adm-select adm-select--sm" style="width:160px" onchange="this.form.submit()">
                <option value="">Any payment</option>
                @foreach(CourseOrder::PAYMENT_STATUSES as $k => $label)<option value="{{ $k }}" @selected(request('payment') === $k)>{{ $label }}</option>@endforeach
            </select>
            <select name="method" class="adm-select adm-select--sm" style="width:140px" onchange="this.form.submit()">
                <option value="">All methods</option>
                <option value="bkash" @selected(request('method') === 'bkash')>bKash</option>
                <option value="nagad" @selected(request('method') === 'nagad')>Nagad</option>
                <option value="bank" @selected(request('method') === 'bank')>Bank</option>
            </select>
            <div class="adm-search" style="width:250px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Invoice, name, mobile, TrxID" style="height:36px"></div>
            @if(request()->hasAny(['status', 'payment', 'method', 'search']))<a href="{{ route('admin.reportsAll', array_merge(['type' => $type], request()->only(['range', 'startDate', 'endDate']))) }}" class="adm-btn adm-btn--sm">Clear</a>@endif
        </form>
    </div>
    @if($orders->isEmpty())
        <div class="adm-empty"><i class="fa-solid fa-receipt"></i><h5>No orders found</h5><p>Try a wider date range or fewer filters.</p></div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Order</th><th>Student</th><th>Payment details</th><th class="num">Subtotal</th><th class="num">Discount</th><th class="num">Total</th><th>Payment</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($orders as $o)
                        <tr>
                            <td><a href="{{ route('admin.course-orders.show', $o) }}" class="adm-mono"><b>#{{ $o->invoice }}</b></a><small class="d-block text-muted">{{ $o->created_at->format('d M Y, h:i A') }}</small></td>
                            <td><strong>{{ $o->name }}</strong><small class="d-block">{{ $o->mobile }}</small><small class="d-block text-muted">{{ $o->email }}</small></td>
                            <td>
                                <span class="adm-badge adm-badge--plain adm-badge--{{ $o->payment_method }}">{{ $o->methodName() }}</span>
                                <small class="d-block mt-1">TrxID: <b class="adm-mono text-dark">{{ $o->transaction_id }}</b></small>
                                <small class="d-block text-muted">From: <span class="adm-mono">{{ $o->sender_number }}</span></small>
                            </td>
                            <td class="num">{{ Storefront::money($o->subtotal) }}</td>
                            <td class="num">{{ Storefront::money($o->discount) }}</td>
                            <td class="num"><b>{{ Storefront::money($o->total) }}</b><small class="d-block text-muted">{{ $o->items_count }} {{ \Illuminate\Support\Str::plural('course', $o->items_count) }}</small></td>
                            <td><span class="adm-badge adm-badge--{{ $o->payment_status }}">{{ CourseOrder::PAYMENT_STATUSES[$o->payment_status] ?? $o->payment_status }}</span></td>
                            <td><span class="adm-badge adm-badge--{{ $o->status }}">{{ CourseOrder::STATUSES[$o->status] ?? $o->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="adm-pager">
            <span>Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders</span>
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
