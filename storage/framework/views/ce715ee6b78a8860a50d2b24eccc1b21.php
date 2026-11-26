

<?php
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
?>

<?php $__env->startSection('title', ($page->seo_title ?? null) ?: $title . ' | ' . Storefront::site()['name']); ?>
<?php $__env->startSection('meta_description', ($page->seo_description ?? null) ?: $intro); ?>

<?php $__env->startSection('content'); ?>
<section class="listing-hero">
    <div class="hero__glow hero__glow--1"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb--light">
                <li class="breadcrumb-item"><a href="<?php echo e(route('index')); ?>">Home</a></li>
                <?php if($category || $type === 'free' || $page): ?>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('courses.index')); ?>">Courses</a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($title); ?></li>
            </ol>
        </nav>
        <?php if($category): ?>
            <span class="listing-hero__icon"><i class="bi <?php echo e($category->icon ?: 'bi-grid'); ?>"></i></span>
        <?php elseif($type === 'free'): ?>
            <span class="listing-hero__icon"><i class="bi bi-youtube"></i></span>
        <?php endif; ?>
        <h1><?php echo e($title); ?></h1>
        <?php if($intro): ?><p><?php echo e($intro); ?></p><?php endif; ?>
        <div class="listing-hero__stats">
            <span><i class="bi bi-collection-play"></i> <?php echo e($courses->total()); ?> <?php echo e(\Illuminate\Support\Str::plural('course', $courses->total())); ?></span>
            <span><i class="bi bi-translate"></i> Taught in Bangla</span>
            <span><i class="bi bi-patch-check"></i> <?php echo e($type === 'free' ? 'No sign-up needed' : 'Pay with bKash, Nagad or bank'); ?></span>
        </div>
        <form class="listing-hero__search" action="<?php echo e($action); ?>" method="get" role="search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search <?php echo e(\Illuminate\Support\Str::lower($title)); ?>…" aria-label="Search courses">
            <?php $__currentLoopData = ['type' => isset($preset['type']) ? null : $type, 'category' => isset($preset['category']) ? null : ($category->slug ?? null), 'rating' => $rating, 'sort' => $sort !== 'popular' ? $sort : null]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($v): ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="level[]" value="<?php echo e($lv); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </div>
</section>

<section class="listing">
    <div class="container">
        <div class="row g-4">
            
            <aside class="col-lg-3 filters-col" id="filtersCol" aria-label="Course filters">
                <div class="filters-drawer__head">
                    <strong><i class="bi bi-sliders"></i> Filters</strong>
                    <button type="button" class="filters-drawer__close" data-filters-close aria-label="Close filters"><i class="bi bi-x-lg"></i></button>
                </div>
                <form class="filters" action="<?php echo e($action); ?>" method="get" data-filter-form>
                    <?php if($q): ?><input type="hidden" name="q" value="<?php echo e($q); ?>"><?php endif; ?>
                    <?php if($sort !== 'popular'): ?><input type="hidden" name="sort" value="<?php echo e($sort); ?>"><?php endif; ?>

                    <div class="filter-box">
                        <h6>Categories</h6>
                        <ul class="filter-list">
                            <li><a href="<?php echo e($link(['category' => null], $type === 'free' && isset($preset['type']) ? route('courses.free') : route('courses.index'))); ?>" class="<?php echo e(!$category ? 'is-active' : ''); ?>"><i class="bi bi-grid"></i> All categories <em><?php echo e($facets['total']); ?></em></a></li>
                            <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a href="<?php echo e($type === 'free' && isset($preset['type']) ? route('courses.free') . '?category=' . $c->slug : route('courses.category', $c->slug) . (($q || $levels || $rating) ? '?' . http_build_query(array_filter(['q' => $q ?: null, 'level' => $levels ?: null, 'rating' => $rating])) : '')); ?>" class="<?php echo e($category && $category->id === $c->id ? 'is-active' : ''); ?>"><i class="bi <?php echo e($c->icon ?: 'bi-grid'); ?>"></i> <?php echo e($c->name); ?> <em><?php echo e($c->courses_count); ?></em></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                    <?php if (! (isset($preset['type']))): ?>
                        <div class="filter-box">
                            <h6>Price</h6>
                            <div class="filter-list">
                                <label class="filter-check"><input type="radio" name="type" value="" <?php if(!$type): echo 'checked'; endif; ?> data-auto> All <em><?php echo e($facets['types']->sum()); ?></em></label>
                                <label class="filter-check"><input type="radio" name="type" value="paid" <?php if($type === 'paid'): echo 'checked'; endif; ?> data-auto> Paid <em><?php echo e($facets['types']['paid'] ?? 0); ?></em></label>
                                <label class="filter-check"><input type="radio" name="type" value="free" <?php if($type === 'free'): echo 'checked'; endif; ?> data-auto> Free <em><?php echo e($facets['types']['free'] ?? 0); ?></em></label>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($category && !isset($preset['category'])): ?><input type="hidden" name="category" value="<?php echo e($category->slug); ?>"><?php endif; ?>

                    <?php if($facets['levels']->count()): ?>
                        <div class="filter-box">
                            <h6>Level</h6>
                            <div class="filter-list">
                                <?php $__currentLoopData = $facets['levels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lv => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="filter-check"><input type="checkbox" name="level[]" value="<?php echo e($lv); ?>" <?php if(in_array($lv, $levels)): echo 'checked'; endif; ?> data-auto> <?php echo e($lv); ?> <em><?php echo e($count); ?></em></label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="filter-box">
                        <h6>Rating</h6>
                        <div class="filter-list">
                            <label class="filter-check"><input type="radio" name="rating" value="" <?php if(!$rating): echo 'checked'; endif; ?> data-auto> Any rating</label>
                            <?php $__currentLoopData = [4, 3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="filter-check"><input type="radio" name="rating" value="<?php echo e($r); ?>" <?php if($rating === $r): echo 'checked'; endif; ?> data-auto> <span class="stars" style="--rating: <?php echo e($r); ?>"></span> <?php echo e($r); ?>.0 &amp; up</label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <noscript><button class="btn btn-primary w-100" type="submit">Apply filters</button></noscript>
                    <?php if($hasFilters): ?>
                        <a href="<?php echo e($action); ?>" class="btn btn-outline-dark w-100"><i class="bi bi-x-circle"></i> Clear all filters</a>
                    <?php endif; ?>
                </form>
            </aside>
            <div class="filters-backdrop" data-filters-close></div>

            
            <div class="col-lg-9">
                <div class="listing-bar">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-dark btn-sm filters-btn" data-filters-toggle><i class="bi bi-sliders"></i> Filters</button>
                        <span class="listing-bar__count">Showing <strong><?php echo e($courses->total() ? $courses->firstItem() . '–' . $courses->lastItem() : 0); ?></strong> of <strong><?php echo e($courses->total()); ?></strong> courses</span>
                    </div>
                    <select class="form-select form-select-sm" aria-label="Sort courses" onchange="window.location.href=this.value">
                        <?php $__currentLoopData = ['popular' => 'Most popular', 'newest' => 'Newest', 'rating' => 'Highest rated', 'price-asc' => 'Price: low to high', 'price-desc' => 'Price: high to low']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($link(['sort' => $k === 'popular' ? null : $k, 'page' => null])); ?>" <?php if($sort === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <?php if($hasFilters): ?>
                    <div class="active-filters">
                        <?php if($q): ?><a href="<?php echo e($link(['q' => null])); ?>">“<?php echo e($q); ?>” <i class="bi bi-x"></i></a><?php endif; ?>
                        <?php if($category && !isset($preset['category'])): ?><a href="<?php echo e($link(['category' => null])); ?>"><?php echo e($category->name); ?> <i class="bi bi-x"></i></a><?php endif; ?>
                        <?php if($type && !isset($preset['type'])): ?><a href="<?php echo e($link(['type' => null])); ?>"><?php echo e(ucfirst($type)); ?> <i class="bi bi-x"></i></a><?php endif; ?>
                        <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a href="<?php echo e($link(['level' => array_values(array_diff($levels, [$lv])) ?: null])); ?>"><?php echo e($lv); ?> <i class="bi bi-x"></i></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($rating): ?><a href="<?php echo e($link(['rating' => null])); ?>"><?php echo e($rating); ?>★ &amp; up <i class="bi bi-x"></i></a><?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if($courses->count()): ?>
                    <div class="course-grid course-grid--3" data-listing-grid>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('frontend.partials.course-card', ['course' => $course], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php echo e($courses->onEachSide(1)->links('pagination::bootstrap-5')); ?>

                <?php else: ?>
                    <div class="empty-state">
                        <span class="empty-state__icon"><i class="bi bi-search"></i></span>
                        <h2>No courses found</h2>
                        <p>Try removing a filter or searching for something else.</p>
                        <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-primary btn-lg">View all courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/courses.blade.php ENDPATH**/ ?>