@extends('admin.layouts.app')
@section('title')<title>Summary Report | {{ general()->title }} Admin</title>@endsection
@php use App\Support\Storefront; $methodNames = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'Bank Transfer']; @endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>Reports</li><li>Summary</li></ul>
        <h1>Summary Report</h1>
        <p>Sales, revenue and order status for course orders in the selected period.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="{{ route('admin.reportsAll', 'order-reports') }}" class="adm-btn"><i class="fa-solid fa-receipt"></i> Order report</a>
        <a href="{{ route('admin.reportsAll', 'summery') }}" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

@include('admin.alerts')
@include('admin.reports._range', ['defaultRange' => 'month'])

@php
    $q = array_filter(['startDate' => $from?->format('Y-m-d'), 'endDate' => $to?->format('Y-m-d'), 'range' => ($from || $to) ? null : 'all']);
    $orderLink = fn ($extra = []) => route('admin.reportsAll', array_merge(['type' => 'order-reports'], $q, $extra));
@endphp

<div class="adm-stats">
    <div class="adm-stat adm-stat--hero">
        <div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="adm-stat__label">Revenue (confirmed)</div>
        <div class="adm-stat__value">{{ Storefront::money($report['revenue']) }}</div>
        <div class="adm-stat__meta"><b>{{ $report['confirmed'] }}</b> confirmed orders &middot; avg {{ Storefront::money($report['avg']) }}</div>
    </div>
    <div class="adm-stat adm-stat--info">
        <div class="adm-stat__icon"><i class="fa-solid fa-cart-shopping"></i></div>
        <div class="adm-stat__label">Total sales</div>
        <div class="adm-stat__value">{{ Storefront::money($report['sales']) }}</div>
        <div class="adm-stat__meta">All orders except cancelled &middot; discount {{ Storefront::money($report['discount']) }}</div>
    </div>
    <div class="adm-stat adm-stat--warn">
        <div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="adm-stat__label">Due (pending / on hold)</div>
        <div class="adm-stat__value">{{ Storefront::money($report['due']) }}</div>
        <div class="adm-stat__meta">{{ $report['pending'] + $report['hold'] }} orders waiting for confirmation</div>
    </div>
    <div class="adm-stat adm-stat--ok">
        <div class="adm-stat__icon"><i class="fa-solid fa-user-graduate"></i></div>
        <div class="adm-stat__label">Students (confirmed)</div>
        <div class="adm-stat__value">{{ number_format($report['students']) }}</div>
        <div class="adm-stat__meta">Unique mobile numbers with a confirmed order</div>
    </div>
</div>

<div class="adm-mini-stats">
    <a href="{{ $orderLink() }}" class="adm-mini" style="--mi:#1E293B;--mi-bg:#F1F3F8"><span class="adm-mini__icon"><i class="fa-solid fa-receipt"></i></span><div><strong>{{ number_format($report['orders']) }}</strong><small>Total orders</small></div></a>
    <a href="{{ $orderLink(['status' => 'pending']) }}" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-clock"></i></span><div><strong>{{ number_format($report['pending']) }}</strong><small>Pending</small></div></a>
    <a href="{{ $orderLink(['status' => 'confirmed']) }}" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-circle-check"></i></span><div><strong>{{ number_format($report['confirmed']) }}</strong><small>Confirmed</small></div></a>
    <a href="{{ $orderLink(['status' => 'hold']) }}" class="adm-mini" style="--mi:#B54708;--mi-bg:#FEF0C7"><span class="adm-mini__icon"><i class="fa-solid fa-pause"></i></span><div><strong>{{ number_format($report['hold']) }}</strong><small>On hold</small></div></a>
    <a href="{{ $orderLink(['status' => 'cancelled']) }}" class="adm-mini" style="--mi:#B42318;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-solid fa-ban"></i></span><div><strong>{{ number_format($report['cancelled']) }}</strong><small>Cancelled &middot; {{ Storefront::money($report['cancelled_amt']) }}</small></div></a>
    <a href="{{ $orderLink(['payment' => 'verified']) }}" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-shield-halved"></i></span><div><strong>{{ number_format($report['verified']) }}</strong><small>Payment verified</small></div></a>
    <a href="{{ $orderLink(['payment' => 'unverified']) }}" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-question"></i></span><div><strong>{{ number_format($report['unverified']) }}</strong><small>Payment unverified</small></div></a>
    <a href="{{ $orderLink(['payment' => 'invalid']) }}" class="adm-mini" style="--mi:#B42318;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-solid fa-triangle-exclamation"></i></span><div><strong>{{ number_format($report['invalid']) }}</strong><small>Payment invalid</small></div></a>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="adm-card">
            <div class="adm-card__head"><div><h3 class="adm-card__title"><i class="fa-solid fa-trophy"></i> Top courses</h3><p class="adm-card__sub">By orders in this period (cancelled excluded)</p></div>
                <a href="{{ route('admin.reportsAll', array_merge(['type' => 'products'], $q)) }}" class="adm-btn adm-btn--sm">Course report</a></div>
            <div class="adm-card__body">
                @forelse($topCourses as $i => $c)
                    <div class="d-flex align-items-center mb-3" style="gap:.75rem">
                        <span class="adm-rank">{{ $i + 1 }}</span>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between" style="gap:.5rem"><strong class="text-dark text-truncate">{{ $c->title }}</strong><small class="text-muted text-nowrap">{{ $c->sales }} orders &middot; {{ Storefront::money($c->revenue) }}</small></div>
                            <div class="adm-progress"><span style="width: {{ max(4, $c->sales / max(1, $topCourses->max('sales')) * 100) }}%"></span></div>
                        </div>
                    </div>
                @empty
                    <div class="adm-empty"><i class="fa-solid fa-layer-group"></i><h5>No course sales</h5><p>No orders in this period.</p></div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="adm-card">
            <div class="adm-card__head"><div><h3 class="adm-card__title"><i class="fa-solid fa-wallet"></i> Payment methods</h3><p class="adm-card__sub">Cancelled orders excluded</p></div></div>
            @if($methods->isEmpty())
                <div class="adm-card__body"><div class="adm-empty"><i class="fa-solid fa-wallet"></i><h5>No payments</h5><p>No orders in this period.</p></div></div>
            @else
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>Method</th><th class="num">Orders</th><th class="num">Amount</th><th class="num">Confirmed</th></tr></thead>
                        <tbody>
                            @foreach($methods as $m)
                                <tr>
                                    <td><span class="adm-badge adm-badge--plain adm-badge--{{ $m['method'] }}">{{ $methodNames[$m['method']] ?? ucfirst($m['method']) }}</span></td>
                                    <td class="num">{{ $m['orders'] }}</td>
                                    <td class="num">{{ Storefront::money($m['amount']) }}</td>
                                    <td class="num"><b>{{ Storefront::money($m['confirmed']) }}</b></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@if($daily->isNotEmpty())
<div class="adm-card">
    <div class="adm-card__head"><div><h3 class="adm-card__title"><i class="fa-solid fa-calendar-days"></i> Daily breakdown</h3><p class="adm-card__sub">Days with orders are highlighted</p></div></div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>Date</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Sales</th><th class="num">Revenue</th></tr></thead>
            <tbody>
                @foreach($daily->reverse() as $d)
                    <tr style="{{ $d['orders'] ? '' : 'opacity:.5' }}">
                        <td>{{ $d['date']->format('d M Y, D') }}</td>
                        <td class="num">{{ $d['orders'] }}</td>
                        <td class="num">{{ $d['confirmed'] }}</td>
                        <td class="num">{{ Storefront::money($d['sales']) }}</td>
                        <td class="num"><b>{{ Storefront::money($d['revenue']) }}</b></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th>Total</th><th class="num">{{ $daily->sum('orders') }}</th><th class="num">{{ $daily->sum('confirmed') }}</th><th class="num">{{ Storefront::money($daily->sum('sales')) }}</th><th class="num">{{ Storefront::money($daily->sum('revenue')) }}</th></tr>
            </tfoot>
        </table>
    </div>
</div>
@endif
@endsection
