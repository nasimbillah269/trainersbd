@extends('admin.layouts.app')
@section('title')<title>Reviews | {{ general()->title }} Admin</title>@endsection

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li><a href="{{ route('admin.courses.index') }}">Courses</a></li><li>Reviews</li></ul>
        <h1>Reviews &amp; ratings</h1>
        <p>Approve student reviews before they appear on course pages.</p>
    </div>
</div>

@include('admin.alerts')

<div class="adm-toolbar">
    <nav class="adm-tabs">
        <a href="{{ route('admin.course-reviews.index') }}" class="{{ !$status ? 'is-active' : '' }}">All <em>{{ $counts->sum() }}</em></a>
        <a href="{{ route('admin.course-reviews.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'is-active' : '' }}">Pending <em>{{ $counts['pending'] ?? 0 }}</em></a>
        <a href="{{ route('admin.course-reviews.index', ['status' => 'approved']) }}" class="{{ $status === 'approved' ? 'is-active' : '' }}">Approved <em>{{ $counts['approved'] ?? 0 }}</em></a>
        <a href="{{ route('admin.course-reviews.index', ['status' => 'rejected']) }}" class="{{ $status === 'rejected' ? 'is-active' : '' }}">Rejected <em>{{ $counts['rejected'] ?? 0 }}</em></a>
    </nav>
    <form method="get">
        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <div class="adm-search" style="width:260px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or text…" style="height:36px"></div>
    </form>
</div>

<div class="adm-card">
    @if($reviews->isEmpty())
        <div class="adm-empty"><i class="fa-regular fa-star"></i><h5>No reviews here</h5><p>Reviews submitted on course pages will appear in this list.</p></div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Student</th><th>Review</th><th>Course</th><th>Status</th><th class="actions">Actions</th></tr></thead>
                <tbody>
                    @foreach($reviews as $rv)
                        <tr>
                            <td style="min-width:200px"><div class="adm-cell"><span class="adm-avatar">{{ $rv->initials() }}</span><div><strong>{{ $rv->name }}</strong><small>{{ $rv->email ?: '—' }}</small><small>{{ $rv->created_at->format('d M Y, h:i A') }}</small></div></div></td>
                            <td style="min-width:300px">
                                <div style="color:#F79009;font-size:.8rem;letter-spacing:1px">@for($s = 1; $s <= 5; $s++)<i class="fa-{{ $s <= $rv->rating ? 'solid' : 'regular' }} fa-star"></i>@endfor</div>
                                <div class="mt-1">{{ $rv->review }}</div>
                            </td>
                            <td>@if($rv->course)<a href="{{ $rv->course->url() }}#reviews" target="_blank">{{ \Illuminate\Support\Str::limit($rv->course->title, 40) }}</a>@else — @endif</td>
                            <td><span class="adm-badge adm-badge--{{ $rv->status }}">{{ ucfirst($rv->status) }}</span></td>
                            <td class="actions">
                                @if($rv->status !== 'approved')
                                    <form method="post" action="{{ route('admin.course-reviews.status', $rv) }}">@csrf<input type="hidden" name="status" value="approved"><button class="adm-btn adm-btn--sm adm-btn--success"><i class="fa-solid fa-check"></i> Approve</button></form>
                                @endif
                                @if($rv->status !== 'rejected')
                                    <form method="post" action="{{ route('admin.course-reviews.status', $rv) }}">@csrf<input type="hidden" name="status" value="rejected"><button class="adm-btn adm-btn--sm adm-btn--warning"><i class="fa-solid fa-ban"></i> Reject</button></form>
                                @endif
                                <form method="post" action="{{ route('admin.course-reviews.destroy', $rv) }}">@csrf<button class="adm-btn adm-btn--sm adm-btn--icon adm-btn--danger" title="Delete" data-confirm="Delete this review permanently?"><i class="fa-regular fa-trash-can"></i></button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="adm-pager"><span>{{ $reviews->total() }} reviews</span>{{ $reviews->links('pagination::bootstrap-4') }}</div>
    @endif
</div>
@endsection
