@extends('frontend.layouts.app')

@php
    use App\Support\Storefront;
    $cats = Storefront::categories()->where('courses_count', '>', 0);
    // Build a URL on the listing with some params changed (null removes a param)
    $link = function (array $changes = [], ?string $base = null) use ($action, $q, $type, $levels, $rating, $sort, $category, $preset) {
        $params = array_filter([
            'q'        => $q ?: null,
            'type'     => isset($preset['type']) ? null : $type,
            'category' => isset($preset['category']) ? null : ($category->slug ?? null),
            'level'    => $levels ?: null,
            'rating'   => $rating,
            'sort'     => $sort !== 'popular' ? $sort : null,
        ]);
        foreach ($changes as $k => $v) {
            if ($v === null) { unset($params[$k]); } else { $params[$k] = $v; }
        }
        $query = http_build_query($params);
        return ($base ?? $action) . ($query ? '?' . $query : '');
    };
    $hasFilters = $q || ($type && !isset($preset['type'])) || ($category && !isset($preset['category'])) || $levels || $rating;
@endphp

@section('title', ($page->seo_title ?? null) ?: $title . ' | ' . Storefront::site()['name'])
@section('meta_description', ($page->seo_description ?? null) ?: $intro)

@section('content')
<section class="listing-hero">
    <div class="hero__glow hero__glow--1"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb--light">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                @if($category || $type === 'free' || $page)
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>
        @if($category)
            <span class="listing-hero__icon"><i class="bi {{ $category->icon ?: 'bi-grid' }}"></i></span>
        @elseif($type === 'free')
            <span class="listing-hero__icon"><i class="bi bi-youtube"></i></span>
        @endif
        <h1>{{ $title }}</h1>
        @if($intro)<p>{{ $intro }}</p>@endif
        <div class="listing-hero__stats">
            <span><i class="bi bi-collection-play"></i> {{ $courses->total() }} {{ \Illuminate\Support\Str::plural('course', $courses->total()) }}</span>
            <span><i class="bi bi-translate"></i> Taught in Bangla</span>
            <span><i class="bi bi-patch-check"></i> {{ $type === 'free' ? 'No sign-up needed' : 'Pay with bKash, Nagad or bank' }}</span>
        </div>
        <form class="listing-hero__search" action="{{ $action }}" method="get" role="search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="{{ $q }}" placeholder="Search {{ \Illuminate\Support\Str::lower($title) }}…" aria-label="Search courses">
            @foreach(['type' => isset($preset['type']) ? null : $type, 'category' => isset($preset['category']) ? null : ($category->slug ?? null), 'rating' => $rating, 'sort' => $sort !== 'popular' ? $sort : null] as $k => $v)
                @if($v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
            @endforeach
            @foreach($levels as $lv)<input type="hidden" name="level[]" value="{{ $lv }}">@endforeach
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
</section>

<section class="listing">
    <div class="container">
        <div class="row g-4">
            {{-- ===== Filters ===== --}}
            <aside class="col-lg-3 filters-col" id="filtersCol" aria-label="Course filters">
                <div class="filters-drawer__head">
                    <strong><i class="bi bi-sliders"></i> Filters</strong>
                    <button type="button" class="filters-drawer__close" data-filters-close aria-label="Close filters"><i class="bi bi-x-lg"></i></button>
                </div>
                <form class="filters" action="{{ $action }}" method="get" data-filter-form>
                    @if($q)<input type="hidden" name="q" value="{{ $q }}">@endif
                    @if($sort !== 'popular')<input type="hidden" name="sort" value="{{ $sort }}">@endif

                    <div class="filter-box">
                        <h6>Categories</h6>
                        <ul class="filter-list">
                            <li><a href="{{ $link(['category' => null], $type === 'free' && isset($preset['type']) ? route('courses.free') : route('courses.index')) }}" class="{{ !$category ? 'is-active' : '' }}"><i class="bi bi-grid"></i> All categories <em>{{ $facets['total'] }}</em></a></li>
                            @foreach($cats as $c)
                                <li><a href="{{ $type === 'free' && isset($preset['type']) ? route('courses.free') . '?category=' . $c->slug : route('courses.category', $c->slug) . (($q || $levels || $rating) ? '?' . http_build_query(array_filter(['q' => $q ?: null, 'level' => $levels ?: null, 'rating' => $rating])) : '') }}" class="{{ $category && $category->id === $c->id ? 'is-active' : '' }}"><i class="bi {{ $c->icon ?: 'bi-grid' }}"></i> {{ $c->name }} <em>{{ $c->courses_count }}</em></a></li>
                            @endforeach
                        </ul>
                    </div>

                    @unless(isset($preset['type']))
                        <div class="filter-box">
                            <h6>Price</h6>
                            <div class="filter-list">
                                <label class="filter-check"><input type="radio" name="type" value="" @checked(!$type) data-auto> All <em>{{ $facets['types']->sum() }}</em></label>
                                <label class="filter-check"><input type="radio" name="type" value="paid" @checked($type === 'paid') data-auto> Paid <em>{{ $facets['types']['paid'] ?? 0 }}</em></label>
                                <label class="filter-check"><input type="radio" name="type" value="free" @checked($type === 'free') data-auto> Free <em>{{ $facets['types']['free'] ?? 0 }}</em></label>
                            </div>
                        </div>
                    @endunless
                    @if($category && !isset($preset['category']))<input type="hidden" name="category" value="{{ $category->slug }}">@endif

                    @if($facets['levels']->count())
                        <div class="filter-box">
                            <h6>Level</h6>
                            <div class="filter-list">
                                @foreach($facets['levels'] as $lv => $count)
                                    <label class="filter-check"><input type="checkbox" name="level[]" value="{{ $lv }}" @checked(in_array($lv, $levels)) data-auto> {{ $lv }} <em>{{ $count }}</em></label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="filter-box">
                        <h6>Rating</h6>
                        <div class="filter-list">
                            <label class="filter-check"><input type="radio" name="rating" value="" @checked(!$rating) data-auto> Any rating</label>
                            @foreach([4, 3] as $r)
                                <label class="filter-check"><input type="radio" name="rating" value="{{ $r }}" @checked($rating === $r) data-auto> <span class="stars" style="--rating: {{ $r }}"></span> {{ $r }}.0 &amp; up</label>
                            @endforeach
                        </div>
                    </div>

                    <noscript><button class="btn btn-primary w-100" type="submit">Apply filters</button></noscript>
                    @if($hasFilters)
                        <a href="{{ $action }}" class="btn btn-outline-dark w-100"><i class="bi bi-x-circle"></i> Clear all filters</a>
                    @endif
                </form>
            </aside>
            <div class="filters-backdrop" data-filters-close></div>

            {{-- ===== Results ===== --}}
            <div class="col-lg-9">
                <div class="listing-bar">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-dark btn-sm filters-btn" data-filters-toggle><i class="bi bi-sliders"></i> Filters</button>
                        <span class="listing-bar__count">Showing <strong>{{ $courses->total() ? $courses->firstItem() . '–' . $courses->lastItem() : 0 }}</strong> of <strong>{{ $courses->total() }}</strong> courses</span>
                    </div>
                    <select class="form-select form-select-sm" aria-label="Sort courses" onchange="window.location.href=this.value">
                        @foreach(['popular' => 'Most popular', 'newest' => 'Newest', 'rating' => 'Highest rated', 'price-asc' => 'Price: low to high', 'price-desc' => 'Price: high to low'] as $k => $label)
                            <option value="{{ $link(['sort' => $k === 'popular' ? null : $k, 'page' => null]) }}" @selected($sort === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @if($hasFilters)
                    <div class="active-filters">
                        @if($q)<a href="{{ $link(['q' => null]) }}">“{{ $q }}” <i class="bi bi-x"></i></a>@endif
                        @if($category && !isset($preset['category']))<a href="{{ $link(['category' => null]) }}">{{ $category->name }} <i class="bi bi-x"></i></a>@endif
                        @if($type && !isset($preset['type']))<a href="{{ $link(['type' => null]) }}">{{ ucfirst($type) }} <i class="bi bi-x"></i></a>@endif
                        @foreach($levels as $lv)<a href="{{ $link(['level' => array_values(array_diff($levels, [$lv])) ?: null]) }}">{{ $lv }} <i class="bi bi-x"></i></a>@endforeach
                        @if($rating)<a href="{{ $link(['rating' => null]) }}">{{ $rating }}★ &amp; up <i class="bi bi-x"></i></a>@endif
                    </div>
                @endif

                @if($courses->count())
                    <div class="course-grid course-grid--3" data-listing-grid>
                        @foreach($courses as $course)
                            @include('frontend.partials.course-card', ['course' => $course])
                        @endforeach
                    </div>
                    {{ $courses->onEachSide(1)->links('pagination::bootstrap-5') }}
                @else
                    <div class="empty-state">
                        <span class="empty-state__icon"><i class="bi bi-search"></i></span>
                        <h2>No courses found</h2>
                        <p>Try removing a filter or searching for something else.</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">View all courses</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
    document.querySelectorAll('[data-filter-form] [data-auto]').forEach(function (el) {
        el.addEventListener('change', function () {
            var form = el.form;
            form.querySelectorAll('input[type=radio][value=""]:checked').forEach(function (r) { r.disabled = true; });
            showListingSkeleton();
            form.submit();
        });
    });
    function showListingSkeleton(e) {
        if (e && e.type === 'click' && (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0)) { return; }
        var grid = document.querySelector('[data-listing-grid]');
        if (!grid) { return; }
        grid.classList.add('is-loading');
        grid.setAttribute('aria-busy', 'true');
        grid.innerHTML = '<div class="sk-course"><span class="sk sk-cover"></span><div class="sk-course__body"><span class="sk sk-line w-50"></span><span class="sk sk-line"></span><span class="sk sk-line w-75"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-course"><span class="sk sk-cover"></span><div class="sk-course__body"><span class="sk sk-line w-50"></span><span class="sk sk-line"></span><span class="sk sk-line w-75"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-course"><span class="sk sk-cover"></span><div class="sk-course__body"><span class="sk sk-line w-50"></span><span class="sk sk-line"></span><span class="sk sk-line w-75"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-course"><span class="sk sk-cover"></span><div class="sk-course__body"><span class="sk sk-line w-50"></span><span class="sk sk-line"></span><span class="sk sk-line w-75"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-course"><span class="sk sk-cover"></span><div class="sk-course__body"><span class="sk sk-line w-50"></span><span class="sk sk-line"></span><span class="sk sk-line w-75"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-course"><span class="sk sk-cover"></span><div class="sk-course__body"><span class="sk sk-line w-50"></span><span class="sk sk-line"></span><span class="sk sk-line w-75"></span><span class="sk sk-line w-25"></span></div></div>';
    }
    document.querySelectorAll('.listing-hero__search, .pagination a').forEach(function (el) {
        el.addEventListener(el.tagName === 'FORM' ? 'submit' : 'click', showListingSkeleton);
    });
    window.addEventListener('pageshow', function (e) { if (e.persisted) { location.reload(); } });
    // Mobile: filters slide in as a drawer from the left
    (function () {
        var col = document.getElementById('filtersCol'), ft = document.querySelector('[data-filters-toggle]');
        if (!col || !ft) { return; }
        function setOpen(open) {
            col.classList.toggle('is-open', open);
            document.body.classList.toggle('filters-open', open);
            ft.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open) { var c = col.querySelector('[data-filters-close]'); if (c) { c.focus({ preventScroll: true }); } }
        }
        ft.setAttribute('aria-controls', 'filtersCol');
        ft.setAttribute('aria-expanded', 'false');
        ft.addEventListener('click', function () { setOpen(!col.classList.contains('is-open')); });
        document.querySelectorAll('[data-filters-close]').forEach(function (el) { el.addEventListener('click', function () { setOpen(false); ft.focus({ preventScroll: true }); }); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && col.classList.contains('is-open')) { setOpen(false); ft.focus({ preventScroll: true }); } });
        window.matchMedia('(min-width: 992px)').addEventListener('change', function (m) { if (m.matches) { setOpen(false); } });
    })();
</script>
@endpush
