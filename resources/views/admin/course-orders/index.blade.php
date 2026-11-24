@extends('admin.layouts.app')
@section('title')<title>Orders | {{ general()->title }} Admin</title>@endsection
@php use App\Support\Storefront; use App\Models\CourseOrder; @endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>Orders</li></ul>
        <h1>Orders</h1>
        <p>Verify manual payments and confirm, hold or cancel orders.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="{{ route('admin.course-orders.index', ['payment' => 'unverified']) }}" class="adm-btn {{ request('payment') === 'unverified' ? 'adm-btn--primary' : '' }}"><i class="fa-solid fa-shield-halved"></i> Unverified payments</a>
    </div>
</div>

@include('admin.alerts')

<div class="adm-toolbar">
    <nav class="adm-tabs">
        <a href="{{ route('admin.course-orders.index') }}" class="{{ !$status && !request('payment') ? 'is-active' : '' }}">All <em>{{ $counts['all'] ?? 0 }}</em></a>
        @foreach(CourseOrder::STATUSES as $key => $label)
            <a href="{{ route('admin.course-orders.index', ['status' => $key]) }}" class="{{ $status === $key ? 'is-active' : '' }}">{{ $label }} <em>{{ $counts[$key] ?? 0 }}</em></a>
        @endforeach
    </nav>
    <form method="get">
        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <select name="method" class="adm-select adm-select--sm" style="width:150px" onchange="this.form.submit()">
            <option value="">All methods</option>
            <option value="bkash" @selected(request('method') === 'bkash')>bKash</option>
            <option value="nagad" @selected(request('method') === 'nagad')>Nagad</option>
            <option value="bank" @selected(request('method') === 'bank')>Bank</option>
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="adm-input adm-input--sm" style="width:150px" title="From date" onchange="this.form.submit()">
        <input type="date" name="to" value="{{ request('to') }}" class="adm-input adm-input--sm" style="width:150px" title="To date" onchange="this.form.submit()">
        <div class="adm-search" style="width:240px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="{{ request('q') }}" placeholder="Invoice, name, mobile, TrxID" style="height:36px"></div>
        @if(request()->hasAny(['q', 'method', 'from', 'to', 'payment']))<a href="{{ route('admin.course-orders.index', array_filter(['status' => $status])) }}" class="adm-btn adm-btn--sm">Clear</a>@endif
    </form>
</div>

<div class="adm-card">
    @if($orders->isEmpty())
        <div class="adm-empty"><i class="fa-solid fa-receipt"></i><h5>No orders found</h5><p>Orders placed from the website checkout will appear here.</p></div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr><th>Order</th><th>Student</th><th>Payment details</th><th class="num">Amount</th><th>Payment</th><th>Status</th><th class="actions">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($orders as $o)
                        <tr>
                            <td><a href="{{ route('admin.course-orders.show', $o) }}" class="adm-mono"><b>#{{ $o->invoice }}</b></a><small class="d-block text-muted">{{ $o->created_at->format('d M Y, h:i A') }}</small></td>
                            <td><div class="adm-cell"><div><strong>{{ $o->name }}</strong><small><a href="tel:{{ $o->mobile }}">{{ $o->mobile }}</a></small><small>{{ $o->email }}</small></div></div></td>
                            <td>
                                <span class="adm-badge adm-badge--plain adm-badge--{{ $o->payment_method }}">{{ $o->methodName() }}</span>
                                <small class="d-block mt-1">TrxID: <b class="adm-mono text-dark">{{ $o->transaction_id }}</b></small>
                                <small class="d-block text-muted">From: <span class="adm-mono">{{ $o->sender_number }}</span></small>
                            </td>
                            <td class="num"><b>{{ Storefront::money($o->total) }}</b><small class="d-block text-muted">{{ $o->items_count }} {{ \Illuminate\Support\Str::plural('course', $o->items_count) }}</small></td>
                            <td><span class="adm-badge adm-badge--{{ $o->payment_status }}">{{ CourseOrder::PAYMENT_STATUSES[$o->payment_status] }}</span></td>
                            <td><span class="adm-badge adm-badge--{{ $o->status }}">{{ CourseOrder::STATUSES[$o->status] }}</span></td>
                            <td class="actions">
                                @if($o->status !== 'confirmed')
                                    <form method="post" action="{{ route('admin.course-orders.quick', $o) }}">@csrf<input type="hidden" name="action" value="confirm"><button class="adm-btn adm-btn--sm adm-btn--success" title="Payment verified, confirm order" data-confirm="Confirm order #{{ $o->invoice }}? This marks the payment ({{ $o->transaction_id }}) as verified."><i class="fa-solid fa-check"></i> Confirm</button></form>
                                @endif
                                <a href="{{ route('admin.course-orders.show', $o) }}" class="adm-btn adm-btn--sm adm-btn--icon" title="Open"><i class="fa-solid fa-arrow-right"></i></a>
                            </td>
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
