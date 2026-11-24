@extends('admin.layouts.app')
@section('title')<title>Students | {{ general()->title }} Admin</title>@endsection
@php use App\Support\Storefront; @endphp

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>Students</li></ul>
        <h1>Students</h1>
        <p>Customer list built from orders (name, mobile and email). Students don't need an account.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="{{ route('admin.students.export') }}" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
    </div>
</div>

<div class="adm-toolbar">
    <span class="text-muted">{{ $students->total() }} unique students</span>
    <form method="get">
        <div class="adm-search" style="width:280px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, mobile or email" style="height:36px"></div>
    </form>
</div>

<div class="adm-card">
    @if($students->isEmpty())
        <div class="adm-empty"><i class="fa-solid fa-user-graduate"></i><h5>No students yet</h5><p>Students appear here after their first order.</p></div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Student</th><th>Mobile</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Spent</th><th>Last order</th><th class="actions"></th></tr></thead>
                <tbody>
                    @foreach($students as $s)
                        <tr>
                            <td><div class="adm-cell"><span class="adm-avatar">{{ Storefront::initials($s->name) }}</span><div><strong>{{ $s->name }}</strong><small>{{ $s->email }}</small></div></div></td>
                            <td class="adm-mono"><a href="tel:{{ $s->mobile }}">{{ $s->mobile }}</a></td>
                            <td class="num">{{ $s->orders }}</td>
                            <td class="num">{{ (int) $s->confirmed }}</td>
                            <td class="num"><b>{{ Storefront::money($s->spent) }}</b></td>
                            <td>{{ \Carbon\Carbon::parse($s->last_order)->format('d M Y') }}</td>
                            <td class="actions"><a href="{{ route('admin.course-orders.index', ['q' => $s->mobile]) }}" class="adm-btn adm-btn--sm">Orders <i class="fa-solid fa-arrow-right"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="adm-pager"><span>Page {{ $students->currentPage() }} of {{ $students->lastPage() }}</span>{{ $students->links('pagination::bootstrap-4') }}</div>
    @endif
</div>
@endsection
