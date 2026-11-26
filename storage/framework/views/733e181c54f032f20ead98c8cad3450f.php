<?php $__env->startSection('title'); ?><title>Summary Report | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>
<?php use App\Support\Storefront; $methodNames = ['bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'Bank Transfer']; ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li>Reports</li><li>Summary</li></ul>
        <h1>Summary Report</h1>
        <p>Sales, revenue and order status for course orders in the selected period.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="<?php echo e(route('admin.reportsAll', 'order-reports')); ?>" class="adm-btn"><i class="fa-solid fa-receipt"></i> Order report</a>
        <a href="<?php echo e(route('admin.reportsAll', 'summery')); ?>" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('admin.reports._range', ['defaultRange' => 'month'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php
    $q = array_filter(['startDate' => $from?->format('Y-m-d'), 'endDate' => $to?->format('Y-m-d'), 'range' => ($from || $to) ? null : 'all']);
    $orderLink = fn ($extra = []) => route('admin.reportsAll', array_merge(['type' => 'order-reports'], $q, $extra));
?>

<div class="adm-stats">
    <div class="adm-stat adm-stat--hero">
        <div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="adm-stat__label">Revenue (confirmed)</div>
        <div class="adm-stat__value"><?php echo e(Storefront::money($report['revenue'])); ?></div>
        <div class="adm-stat__meta"><b><?php echo e($report['confirmed']); ?></b> confirmed orders &middot; avg <?php echo e(Storefront::money($report['avg'])); ?></div>
    </div>
    <div class="adm-stat adm-stat--info">
        <div class="adm-stat__icon"><i class="fa-solid fa-cart-shopping"></i></div>
        <div class="adm-stat__label">Total sales</div>
        <div class="adm-stat__value"><?php echo e(Storefront::money($report['sales'])); ?></div>
        <div class="adm-stat__meta">All orders except cancelled &middot; discount <?php echo e(Storefront::money($report['discount'])); ?></div>
    </div>
    <div class="adm-stat adm-stat--warn">
        <div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="adm-stat__label">Due (pending / on hold)</div>
        <div class="adm-stat__value"><?php echo e(Storefront::money($report['due'])); ?></div>
        <div class="adm-stat__meta"><?php echo e($report['pending'] + $report['hold']); ?> orders waiting for confirmation</div>
    </div>
    <div class="adm-stat adm-stat--ok">
        <div class="adm-stat__icon"><i class="fa-solid fa-user-graduate"></i></div>
        <div class="adm-stat__label">Students (confirmed)</div>
        <div class="adm-stat__value"><?php echo e(number_format($report['students'])); ?></div>
        <div class="adm-stat__meta">Unique mobile numbers with a confirmed order</div>
    </div>
</div>

<div class="adm-mini-stats">
    <a href="<?php echo e($orderLink()); ?>" class="adm-mini" style="--mi:#1E293B;--mi-bg:#F1F3F8"><span class="adm-mini__icon"><i class="fa-solid fa-receipt"></i></span><div><strong><?php echo e(number_format($report['orders'])); ?></strong><small>Total orders</small></div></a>
    <a href="<?php echo e($orderLink(['status' => 'pending'])); ?>" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-clock"></i></span><div><strong><?php echo e(number_format($report['pending'])); ?></strong><small>Pending</small></div></a>
    <a href="<?php echo e($orderLink(['status' => 'confirmed'])); ?>" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-circle-check"></i></span><div><strong><?php echo e(number_format($report['confirmed'])); ?></strong><small>Confirmed</small></div></a>
    <a href="<?php echo e($orderLink(['status' => 'hold'])); ?>" class="adm-mini" style="--mi:#B54708;--mi-bg:#FEF0C7"><span class="adm-mini__icon"><i class="fa-solid fa-pause"></i></span><div><strong><?php echo e(number_format($report['hold'])); ?></strong><small>On hold</small></div></a>
    <a href="<?php echo e($orderLink(['status' => 'cancelled'])); ?>" class="adm-mini" style="--mi:#B42318;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-solid fa-ban"></i></span><div><strong><?php echo e(number_format($report['cancelled'])); ?></strong><small>Cancelled &middot; <?php echo e(Storefront::money($report['cancelled_amt'])); ?></small></div></a>
    <a href="<?php echo e($orderLink(['payment' => 'verified'])); ?>" class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-shield-halved"></i></span><div><strong><?php echo e(number_format($report['verified'])); ?></strong><small>Payment verified</small></div></a>
    <a href="<?php echo e($orderLink(['payment' => 'unverified'])); ?>" class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-question"></i></span><div><strong><?php echo e(number_format($report['unverified'])); ?></strong><small>Payment unverified</small></div></a>
    <a href="<?php echo e($orderLink(['payment' => 'invalid'])); ?>" class="adm-mini" style="--mi:#B42318;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-solid fa-triangle-exclamation"></i></span><div><strong><?php echo e(number_format($report['invalid'])); ?></strong><small>Payment invalid</small></div></a>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="adm-card">
            <div class="adm-card__head"><div><h3 class="adm-card__title"><i class="fa-solid fa-trophy"></i> Top courses</h3><p class="adm-card__sub">By orders in this period (cancelled excluded)</p></div>
                <a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => 'products'], $q))); ?>" class="adm-btn adm-btn--sm">Course report</a></div>
            <div class="adm-card__body">
                <?php $__empty_1 = true; $__currentLoopData = $topCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-center mb-3" style="gap:.75rem">
                        <span class="adm-rank"><?php echo e($i + 1); ?></span>
                        <div style="flex:1;min-width:0">
                            <div class="d-flex justify-content-between" style="gap:.5rem"><strong class="text-dark text-truncate"><?php echo e($c->title); ?></strong><small class="text-muted text-nowrap"><?php echo e($c->sales); ?> orders &middot; <?php echo e(Storefront::money($c->revenue)); ?></small></div>
                            <div class="adm-progress"><span style="width: <?php echo e(max(4, $c->sales / max(1, $topCourses->max('sales')) * 100)); ?>%"></span></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="adm-empty"><i class="fa-solid fa-layer-group"></i><h5>No course sales</h5><p>No orders in this period.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="adm-card">
            <div class="adm-card__head"><div><h3 class="adm-card__title"><i class="fa-solid fa-wallet"></i> Payment methods</h3><p class="adm-card__sub">Cancelled orders excluded</p></div></div>
            <?php if($methods->isEmpty()): ?>
                <div class="adm-card__body"><div class="adm-empty"><i class="fa-solid fa-wallet"></i><h5>No payments</h5><p>No orders in this period.</p></div></div>
            <?php else: ?>
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>Method</th><th class="num">Orders</th><th class="num">Amount</th><th class="num">Confirmed</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><span class="adm-badge adm-badge--plain adm-badge--<?php echo e($m['method']); ?>"><?php echo e($methodNames[$m['method']] ?? ucfirst($m['method'])); ?></span></td>
                                    <td class="num"><?php echo e($m['orders']); ?></td>
                                    <td class="num"><?php echo e(Storefront::money($m['amount'])); ?></td>
                                    <td class="num"><b><?php echo e(Storefront::money($m['confirmed'])); ?></b></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($daily->isNotEmpty()): ?>
<div class="adm-card">
    <div class="adm-card__head"><div><h3 class="adm-card__title"><i class="fa-solid fa-calendar-days"></i> Daily breakdown</h3><p class="adm-card__sub">Days with orders are highlighted</p></div></div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>Date</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Sales</th><th class="num">Revenue</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $daily->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="<?php echo e($d['orders'] ? '' : 'opacity:.5'); ?>">
                        <td><?php echo e($d['date']->format('d M Y, D')); ?></td>
                        <td class="num"><?php echo e($d['orders']); ?></td>
                        <td class="num"><?php echo e($d['confirmed']); ?></td>
                        <td class="num"><?php echo e(Storefront::money($d['sales'])); ?></td>
                        <td class="num"><b><?php echo e(Storefront::money($d['revenue'])); ?></b></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr><th>Total</th><th class="num"><?php echo e($daily->sum('orders')); ?></th><th class="num"><?php echo e($daily->sum('confirmed')); ?></th><th class="num"><?php echo e(Storefront::money($daily->sum('sales'))); ?></th><th class="num"><?php echo e(Storefront::money($daily->sum('revenue'))); ?></th></tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/reports/summeryReports.blade.php ENDPATH**/ ?>