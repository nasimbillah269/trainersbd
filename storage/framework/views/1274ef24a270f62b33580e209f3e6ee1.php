<?php $__env->startSection('title'); ?><title>Dashboard | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>

<?php
    use App\Support\Storefront;
    $maxRev = max(1, collect($chart)->max('revenue'));
    $maxOrd = max(1, collect($chart)->max('orders'));
    $hour = now()->hour;
    $greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <h1><?php echo e($greet); ?>, <?php echo e(\Illuminate\Support\Str::before(Auth::user()->name, ' ') ?: 'Admin'); ?> 👋</h1>
        <p>Here's what's happening with <?php echo e(general()->title); ?> today, <?php echo e(now()->format('l, d F Y')); ?>.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="<?php echo e(route('admin.course-orders.index', ['payment' => 'unverified'])); ?>" class="adm-btn"><i class="fa-solid fa-shield-halved"></i> Verify payments</a>
        <a href="<?php echo e(route('admin.courses.create')); ?>" class="adm-btn adm-btn--primary"><i class="fa-solid fa-plus"></i> Add Course</a>
    </div>
</div>

<?php if($stats['unverified']): ?>
    <div class="adm-alert adm-alert--warning">
        <i class="fa-solid fa-hourglass-half"></i>
        <div><strong><?php echo e($stats['unverified']); ?> <?php echo e(\Illuminate\Support\Str::plural('payment', $stats['unverified'])); ?> waiting for verification.</strong> Check the transaction IDs in your bKash / Nagad / bank statement and confirm the orders.
            <a href="<?php echo e(route('admin.course-orders.index', ['payment' => 'unverified'])); ?>" class="ml-1"><b>Review now &rarr;</b></a></div>
    </div>
<?php endif; ?>


<div class="adm-mini-stats">
    <a href="<?php echo e(route('admin.courses.index')); ?>" class="adm-mini"><span class="adm-mini__icon"><i class="fa-solid fa-layer-group"></i></span><div><strong><?php echo e(number_format($classic['courses'])); ?></strong><small>Courses</small></div></a>
    <a href="<?php echo e(route('admin.students.index')); ?>" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-users"></i></span><div><strong><?php echo e(number_format($classic['customers'])); ?></strong><small>Customers</small></div></a>
    <a href="<?php echo e(route('admin.usersAdmin')); ?>" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-user-shield"></i></span><div><strong><?php echo e(number_format($classic['admins'])); ?></strong><small>Admin Users</small></div></a>
    <a href="<?php echo e(route('admin.pages')); ?>" class="adm-mini" style="--mi:#0086C9;--mi-bg:#F0F9FF"><span class="adm-mini__icon"><i class="fa-regular fa-file-lines"></i></span><div><strong><?php echo e(number_format($classic['pages'])); ?></strong><small>Pages</small></div></a>

    <div class="adm-mini" style="--mi:#003080"><span class="adm-mini__icon"><i class="fa-solid fa-calendar-day"></i></span><div><strong><?php echo e(Storefront::money($classic['sale_today'])); ?></strong><small>Today's sales</small></div></div>
    <div class="adm-mini" style="--mi:#0d47b8;--mi-bg:#F4F3FF"><span class="adm-mini__icon"><i class="fa-solid fa-calendar-week"></i></span><div><strong><?php echo e(Storefront::money($classic['sale_month'])); ?></strong><small>Monthly sales</small></div></div>
    <div class="adm-mini" style="--mi:#C11574;--mi-bg:#FDF2F8"><span class="adm-mini__icon"><i class="fa-solid fa-calendar"></i></span><div><strong><?php echo e(Storefront::money($classic['sale_year'])); ?></strong><small>Yearly sales</small></div></div>
    <a href="<?php echo e(route('admin.course-orders.index')); ?>" class="adm-mini" style="--mi:#1E293B;--mi-bg:#F1F3F8"><span class="adm-mini__icon"><i class="fa-solid fa-receipt"></i></span><div><strong><?php echo e(number_format($classic['orders'])); ?></strong><small>Orders</small></div></a>

    <a href="<?php echo e(route('admin.course-orders.index', ['status' => 'pending'])); ?>" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-clock"></i></span><div><strong><?php echo e(number_format($classic['pending'])); ?></strong><small>Pending Orders</small></div></a>
    <a href="<?php echo e(route('admin.course-orders.index', ['status' => 'confirmed'])); ?>" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-circle-check"></i></span><div><strong><?php echo e(number_format($classic['confirmed'])); ?></strong><small>Confirmed Orders</small></div></a>
    <a href="<?php echo e(route('admin.course-orders.index', ['status' => 'hold'])); ?>" class="adm-mini" style="--mi:#B54708;--mi-bg:#FEF0C7"><span class="adm-mini__icon"><i class="fa-solid fa-pause"></i></span><div><strong><?php echo e(number_format($classic['hold'])); ?></strong><small>On Hold Orders</small></div></a>
    <a href="<?php echo e(route('admin.course-orders.index', ['status' => 'cancelled'])); ?>" class="adm-mini" style="--mi:#B42318;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-solid fa-ban"></i></span><div><strong><?php echo e(number_format($classic['cancelled'])); ?></strong><small>Cancelled Orders</small></div></a>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card__head">
                <div>
                    <h3 class="adm-card__title"><i class="fa-solid fa-chart-column"></i> Sales overview</h3>
                    <p class="adm-card__sub">Confirmed revenue and orders, last 14 days</p>
                </div>
                <div class="d-flex gap-3" style="gap:1.25rem">
                    <div><small class="text-muted d-block">Revenue</small><strong class="text-dark"><?php echo e(Storefront::money(collect($chart)->sum('revenue'))); ?></strong></div>
                    <div><small class="text-muted d-block">Orders</small><strong class="text-dark"><?php echo e(collect($chart)->sum('orders')); ?></strong></div>
                </div>
            </div>
            <div class="adm-card__body">
                <div class="adm-chart">
                    <?php $__currentLoopData = $chart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($h = $day['revenue'] > 0 ? max(4, $day['revenue'] / $maxRev * 100) : ($day['orders'] > 0 ? max(4, $day['orders'] / $maxOrd * 30) : 3)); ?>
                        <div class="adm-chart__col">
                            <div class="adm-chart__bars">
                                <div class="adm-chart__bar <?php echo e($day['revenue'] > 0 ? '' : 'adm-chart__bar--empty'); ?>" style="height: <?php echo e($h); ?>%"></div>
                                <span class="adm-chart__tip"><?php echo e($day['label']); ?>: <?php echo e(Storefront::money($day['revenue'])); ?> &middot; <?php echo e($day['orders']); ?> orders</span>
                            </div>
                            <span class="adm-chart__label"><?php echo e(\Illuminate\Support\Str::before($day['label'], ' ')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head">
                <h3 class="adm-card__title"><i class="fa-solid fa-clock-rotate-left"></i> Recent orders</h3>
                <a href="<?php echo e(route('admin.course-orders.index')); ?>" class="adm-btn adm-btn--sm">View all</a>
            </div>
            <?php if($recentOrders->isEmpty()): ?>
                <div class="adm-empty"><i class="fa-solid fa-receipt"></i><h5>No orders yet</h5><p>New orders from the checkout page will appear here.</p></div>
            <?php else: ?>
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>Order</th><th>Student</th><th>Payment</th><th>Status</th><th class="num">Total</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><a href="<?php echo e(route('admin.course-orders.show', $o)); ?>" class="adm-mono"><b>#<?php echo e($o->invoice); ?></b></a><small class="d-block text-muted"><?php echo e($o->created_at->diffForHumans()); ?></small></td>
                                    <td><div class="adm-cell"><div><strong><?php echo e($o->name); ?></strong><small><?php echo e($o->mobile); ?></small></div></div></td>
                                    <td><span class="adm-badge adm-badge--plain adm-badge--<?php echo e($o->payment_method); ?>"><?php echo e($o->methodName()); ?></span><small class="d-block text-muted adm-mono mt-1"><?php echo e($o->transaction_id); ?></small></td>
                                    <td><span class="adm-badge adm-badge--<?php echo e($o->status); ?>"><?php echo e(\App\Models\CourseOrder::STATUSES[$o->status]); ?></span></td>
                                    <td class="num"><b><?php echo e(Storefront::money($o->total)); ?></b><small class="d-block text-muted"><?php echo e($o->items_count); ?> <?php echo e(\Illuminate\Support\Str::plural('course', $o->items_count)); ?></small></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="adm-card">
            <div class="adm-card__head">
                <h3 class="adm-card__title"><i class="fa-solid fa-layer-group"></i> Latest Courses</h3>
                <a href="<?php echo e(route('admin.courses.index')); ?>" class="adm-btn adm-btn--sm">All</a>
            </div>
            <ul class="adm-list">
                <?php $__empty_1 = true; $__currentLoopData = $latestCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li>
                        <?php if($lc->thumbnailUrl()): ?><img src="<?php echo e($lc->thumbnailUrl()); ?>" class="adm-thumb" style="width:64px" alt=""><?php else: ?><span class="adm-thumb" style="width:64px"><i class="fa-regular fa-image"></i></span><?php endif; ?>
                        <div class="adm-list__main">
                            <a href="<?php echo e(route('admin.courses.edit', $lc)); ?>"><strong><?php echo e($lc->title); ?></strong></a>
                            <small><?php echo e($lc->isFree() ? 'Free' : Storefront::money($lc->price)); ?> &middot; <?php echo e($lc->lessons_count); ?> lessons &middot; <span class="adm-badge adm-badge--<?php echo e($lc->status); ?>" style="font-size:.66rem;padding:.05rem .45rem"><?php echo e($lc->status === 'active' ? 'Published' : 'Draft'); ?></span></small>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li><small class="text-muted">No courses yet.</small></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-trophy"></i> Top selling courses</h3></div>
            <?php if($topCourses->isEmpty()): ?>
                <div class="adm-empty"><i class="fa-solid fa-chart-simple"></i><p class="mb-0">No sales yet.</p></div>
            <?php else: ?>
                <?php ($maxSales = max(1, $topCourses->max('sales'))); ?>
                <ul class="adm-list">
                    <?php $__currentLoopData = $topCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $tc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <span class="adm-rank"><?php echo e($i + 1); ?></span>
                            <div class="adm-list__main">
                                <strong><?php echo e($tc->title); ?></strong>
                                <small><?php echo e($tc->sales); ?> sold &middot; <?php echo e(Storefront::money($tc->amount)); ?></small>
                                <div class="adm-progress"><span style="width: <?php echo e($tc->sales / $maxSales * 100); ?>%"></span></div>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-wallet"></i> Payment methods</h3></div>
            <div class="adm-card__body">
                <?php ($totalM = max(1, $methodSplit->sum('c'))); ?>
                <?php $__empty_1 = true; $__currentLoopData = $methodSplit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($label = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'Bank Transfer'][$m->payment_method] ?? $m->payment_method); ?>
                    <?php ($color = ['bkash' => '#E2136E', 'nagad' => '#F15A22', 'bank' => '#1E40AF'][$m->payment_method] ?? '#003080'); ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between"><strong class="text-dark" style="font-size:.88rem"><?php echo e($label); ?></strong><small class="text-muted"><?php echo e($m->c); ?> orders &middot; <?php echo e(Storefront::money($m->amount)); ?></small></div>
                        <div class="adm-progress"><span style="width: <?php echo e($m->c / $totalM * 100); ?>%; background: <?php echo e($color); ?>"></span></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted mb-0">No payments yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-bolt"></i> Quick actions</h3></div>
            <div class="adm-card__body d-grid" style="display:grid; gap:.6rem">
                <a href="<?php echo e(route('admin.courses.create')); ?>" class="adm-btn adm-btn--block"><i class="fa-solid fa-plus"></i> Add a new course</a>
                <a href="<?php echo e(route('admin.course-reviews.index', ['status' => 'pending'])); ?>" class="adm-btn adm-btn--block"><i class="fa-regular fa-star"></i> Moderate reviews <?php if($stats['reviews_pending']): ?><span class="adm-badge adm-badge--pending ml-1"><?php echo e($stats['reviews_pending']); ?></span><?php endif; ?></a>
                <a href="<?php echo e(route('admin.payment-settings')); ?>" class="adm-btn adm-btn--block"><i class="fa-solid fa-wallet"></i> Update payment numbers</a>
                <a href="<?php echo e(route('admin.sliders')); ?>" class="adm-btn adm-btn--block"><i class="fa-regular fa-images"></i> Edit home slider</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>