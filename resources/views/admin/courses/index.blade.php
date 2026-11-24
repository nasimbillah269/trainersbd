@extends('admin.layouts.app')
@php
    use App\Support\Storefront;
    $freeSection = $freeSection ?? false;
    $listRoute = $freeSection ? 'admin.free-courses.index' : 'admin.courses.index';
    $addUrl = $freeSection ? route('admin.courses.create', ['type' => 'free']) : route('admin.courses.create');
@endphp
@section('title')<title>{{ $freeSection ? 'Free Courses' : 'Courses' }} | {{ general()->title }} Admin</title>@endsection

@section('contents')
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li>{{ $freeSection ? 'Free Courses' : 'Courses' }}</li></ul>
        @if($freeSection)
            <h1><i class="fa-brands fa-youtube" style="color:#FF0000"></i> Free Courses</h1>
            <p>YouTube courses students can watch on the website without payment or sign-up.</p>
        @else
            <h1>Courses</h1>
            <p>Create, publish and organise your paid and free courses.</p>
        @endif
    </div>
    <div class="adm-page-head__actions">
        @if($freeSection)
            <a href="{{ route('courses.free') }}" target="_blank" class="adm-btn"><i class="fa-regular fa-eye"></i> View on website</a>
        @else
            <a href="{{ route('admin.course-categories.index') }}" class="adm-btn"><i class="fa-solid fa-shapes"></i> Categories</a>
        @endif
        <a href="{{ $addUrl }}" class="adm-btn adm-btn--primary"><i class="fa-solid fa-plus"></i> {{ $freeSection ? 'Add Free Course' : 'Add Course' }}</a>
    </div>
</div>

@include('admin.alerts')

@if($freeSection)
    <div class="adm-mini-stats" style="grid-template-columns:repeat(4,minmax(0,1fr))">
        <div class="adm-mini" style="--mi:#FF0000;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-brands fa-youtube"></i></span><div><strong>{{ $counts['all'] }}</strong><small>Free courses</small></div></div>
        <div class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-globe"></i></span><div><strong>{{ $counts['active'] }}</strong><small>Published</small></div></div>
        <div class="adm-mini"><span class="adm-mini__icon"><i class="fa-solid fa-circle-play"></i></span><div><strong>{{ $counts['lessons'] }}</strong><small>Video lessons</small></div></div>
        <div class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-users"></i></span><div><strong>{{ number_format($counts['students']) }}</strong><small>Learners (display)</small></div></div>
    </div>
@endif

<div class="adm-toolbar">
    <nav class="adm-tabs">
        <a href="{{ route($listRoute) }}" class="{{ !request('status') && !request('type') ? 'is-active' : '' }}">All <em>{{ $counts['all'] }}</em></a>
        <a href="{{ route($listRoute, ['status' => 'active']) }}" class="{{ request('status') === 'active' ? 'is-active' : '' }}">Published <em>{{ $counts['active'] }}</em></a>
        <a href="{{ route($listRoute, ['status' => 'draft']) }}" class="{{ request('status') === 'draft' ? 'is-active' : '' }}">Drafts <em>{{ $counts['draft'] }}</em></a>
        @unless($freeSection)
            <a href="{{ route('admin.courses.index', ['type' => 'paid']) }}" class="{{ request('type') === 'paid' ? 'is-active' : '' }}">Paid <em>{{ $counts['paid'] }}</em></a>
            <a href="{{ route('admin.free-courses.index') }}">Free <em>{{ $counts['free'] }}</em></a>
        @endunless
    </nav>
    <form method="get" action="{{ route($listRoute) }}">
        @foreach(['status', 'type'] as $keep)
            @if(request($keep))<input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}">@endif
        @endforeach
        <select name="category" class="adm-select adm-select--sm" style="width:190px" onchange="this.form.submit()">
            <option value="">All categories</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <div class="adm-search" style="width:240px">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search courses…" style="height:36px">
        </div>
    </form>
</div>

<div class="adm-card">
    @if($courses->isEmpty())
        <div class="adm-empty">
            <i class="fa-solid fa-layer-group"></i>
            <h5>No {{ $freeSection ? 'free ' : '' }}courses found</h5>
            <p>Try a different filter, or add your first {{ $freeSection ? 'free ' : '' }}course.</p>
            <a href="{{ $addUrl }}" class="adm-btn adm-btn--primary mt-2"><i class="fa-solid fa-plus"></i> {{ $freeSection ? 'Add Free Course' : 'Add Course' }}</a>
        </div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Category</th>
                        @unless($freeSection)<th class="num">Price</th>@endunless
                        <th class="num">Lessons</th>
                        @unless($freeSection)<th class="num">Sales</th>@endunless
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                        <tr>
                            <td style="min-width:320px">
                                <div class="adm-cell">
                                    @if($course->thumbnailUrl())
                                        <img src="{{ $course->thumbnailUrl() }}" class="adm-thumb" alt="">
                                    @else
                                        <span class="adm-thumb"><i class="fa-regular fa-image"></i></span>
                                    @endif
                                    <a href="{{ route('admin.courses.edit', $course) }}">
                                        <strong>{{ $course->title }}</strong>
                                        <small>{{ $course->instructor->name ?? 'No instructor' }} &middot; Updated {{ $course->updated_at->diffForHumans() }}</small>
                                    </a>
                                </div>
                            </td>
                            <td>{{ $course->category->name ?? '—' }}</td>
                            @unless($freeSection)<td class="num">
                                @if($course->isFree())
                                    <span class="adm-badge adm-badge--free">Free</span>
                                @else
                                    <b>{{ Storefront::money($course->price) }}</b>
                                    @if($course->discountPercent())<small class="d-block text-muted"><s>{{ Storefront::money($course->old_price) }}</s> &middot; {{ $course->discountPercent() }}% off</small>@endif
                                @endif
                            </td>@endunless
                            <td class="num">{{ $course->lessons_count }}</td>
                            @unless($freeSection)<td class="num">{{ $course->sales_count }}</td>@endunless
                            <td>
                                @if($course->reviews_count)
                                    <span style="color:#B54708;font-weight:700"><i class="fa-solid fa-star" style="color:#F79009"></i> {{ number_format($course->rating_avg, 1) }}</span>
                                    <small class="text-muted">({{ $course->reviews_count }})</small>
                                @else
                                    <small class="text-muted">No reviews</small>
                                @endif
                            </td>
                            <td>
                                <label class="adm-switch" title="Published">
                                    <input type="checkbox" data-quick="{{ route('admin.courses.quick', $course) }}" data-field="status" @checked($course->status === 'active')>
                                    <span class="adm-switch__ui"></span>
                                    <span class="adm-badge adm-badge--{{ $course->status }}" data-status-label>{{ $course->status === 'active' ? 'Published' : 'Draft' }}</span>
                                </label>
                            </td>
                            <td>
                                <label class="adm-switch" title="Featured on home page">
                                    <input type="checkbox" data-quick="{{ route('admin.courses.quick', $course) }}" data-field="featured" @checked($course->featured)>
                                    <span class="adm-switch__ui"></span>
                                </label>
                            </td>
                            <td class="actions">
                                <a href="{{ $course->url() }}" target="_blank" class="adm-btn adm-btn--sm adm-btn--icon" title="View on website"><i class="fa-regular fa-eye"></i></a>
                                <a href="{{ route('admin.courses.edit', $course) }}" class="adm-btn adm-btn--sm adm-btn--icon" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                                <form method="post" action="{{ route('admin.courses.duplicate', $course) }}">@csrf<button class="adm-btn adm-btn--sm adm-btn--icon" title="Duplicate"><i class="fa-regular fa-copy"></i></button></form>
                                <form method="post" action="{{ route('admin.courses.destroy', $course) }}">@csrf<button class="adm-btn adm-btn--sm adm-btn--icon adm-btn--danger" title="Delete" data-confirm="Delete “{{ $course->title }}”? Its modules, lessons and reviews will be removed too."><i class="fa-regular fa-trash-can"></i></button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="adm-pager">
            <span>Showing {{ $courses->firstItem() }}–{{ $courses->lastItem() }} of {{ $courses->total() }} courses</span>
            {{ $courses->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection

@push('js')
<script>
    $(document).on('change', '[data-quick]', function () {
        var input = this, field = input.dataset.field;
        var value = field === 'status' ? (input.checked ? 'active' : 'draft') : (input.checked ? 1 : 0);
        $.post(input.dataset.quick, { field: field, value: value }).done(function () {
            if (field === 'status') {
                var label = $(input).closest('label').find('[data-status-label]');
                label.text(input.checked ? 'Published' : 'Draft').attr('class', 'adm-badge adm-badge--' + (input.checked ? 'active' : 'draft'));
            }
        }).fail(function () { input.checked = !input.checked; alert('Could not update. Please try again.'); });
    });
</script>
@endpush
