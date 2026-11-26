<?php $__env->startSection('title'); ?><title>Order Report | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>
<?php use App\Support\Storefront; use App\Models\CourseOrder; ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li>Reports</li><li>Orders</li></ul>
        <h1>Order Report</h1>
        <p>All course orders with payment details. Filter, then export to CSV.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => $type], request()->except('page'), ['export' => 'csv']))); ?>" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="<?php echo e(route('admin.reportsAll', $type)); ?>" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('admin.reports._range', ['defaultRange' => 'all'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="adm-stats">
    <div class="adm-stat"><div class="adm-stat__icon"><i class="fa-solid fa-receipt"></i></div><div class="adm-stat__label">Orders</div><div class="adm-stat__value"><?php echo e(number_format($totals->orders)); ?></div><div class="adm-stat__meta">Total <?php echo e(Storefront::money($totals->amount)); ?> &middot; discount <?php echo e(Storefront::money($totals->discount)); ?></div></div>
    <div class="adm-stat adm-stat--hero"><div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div><div class="adm-stat__label">Revenue (confirmed)</div><div class="adm-stat__value"><?php echo e(Storefront::money($totals->revenue)); ?></div></div>
    <div class="adm-stat adm-stat--warn"><div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div><div class="adm-stat__label">Due (pending / hold)</div><div class="adm-stat__value"><?php echo e(Storefront::money($totals->due)); ?></div></div>
    <div class="adm-stat adm-stat--info"><div class="adm-stat__icon"><i class="fa-solid fa-calculator"></i></div><div class="adm-stat__label">Average order</div><div class="adm-stat__value"><?php echo e(Storefront::money($totals->orders ? $totals->amount / $totals->orders : 0)); ?></div></div>
</div>

<div class="adm-card">
    <div class="adm-card__head">
        <form method="get" action="<?php echo e(route('admin.reportsAll', $type)); ?>" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%">
            <?php $__currentLoopData = request()->only(['range', 'startDate', 'endDate']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <select name="status" class="adm-select adm-select--sm" style="width:150px" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <?php $__currentLoopData = CourseOrder::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('status') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="payment" class="adm-select adm-select--sm" style="width:160px" onchange="this.form.submit()">
                <option value="">Any payment</option>
                <?php $__currentLoopData = CourseOrder::PAYMENT_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('payment') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="method" class="adm-select adm-select--sm" style="width:140px" onchange="this.form.submit()">
                <option value="">All methods</option>
                <option value="bkash" <?php if(request('method') === 'bkash'): echo 'selected'; endif; ?>>bKash</option>
                <option value="nagad" <?php if(request('method') === 'nagad'): echo 'selected'; endif; ?>>Nagad</option>
                <option value="bank" <?php if(request('method') === 'bank'): echo 'selected'; endif; ?>>Bank</option>
            </select>
            <div class="adm-search" style="width:250px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Invoice, name, mobile, TrxID" style="height:36px"></div>
            <?php if(request()->hasAny(['status', 'payment', 'method', 'search'])): ?><a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => $type], request()->only(['range', 'startDate', 'endDate'])))); ?>" class="adm-btn adm-btn--sm">Clear</a><?php endif; ?>
        </form>
    </div>
    <?php if($orders->isEmpty()): ?>
        <div class="adm-empty"><i class="fa-solid fa-receipt"></i><h5>No orders found</h5><p>Try a wider date range or fewer filters.</p></div>
    <?php else: ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Order</th><th>Student</th><th>Payment details</th><th class="num">Subtotal</th><th class="num">Discount</th><th class="num">Total</th><th>Payment</th><th>Status</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><a href="<?php echo e(route('admin.course-orders.show', $o)); ?>" class="adm-mono"><b>#<?php echo e($o->invoice); ?></b></a><small class="d-block text-muted"><?php echo e($o->created_at->format('d M Y, h:i A')); ?></small></td>
                            <td><strong><?php echo e($o->name); ?></strong><small class="d-block"><?php echo e($o->mobile); ?></small><small class="d-block text-muted"><?php echo e($o->email); ?></small></td>
                            <td>
                                <span class="adm-badge adm-badge--plain adm-badge--<?php echo e($o->payment_method); ?>"><?php echo e($o->methodName()); ?></span>
                                <small class="d-block mt-1">TrxID: <b class="adm-mono text-dark"><?php echo e($o->transaction_id); ?></b></small>
                                <small class="d-block text-muted">From: <span class="adm-mono"><?php echo e($o->sender_number); ?></span></small>
                            </td>
                            <td class="num"><?php echo e(Storefront::money($o->subtotal)); ?></td>
                            <td class="num"><?php echo e(Storefront::money($o->discount)); ?></td>
                            <td class="num"><b><?php echo e(Storefront::money($o->total)); ?></b><small class="d-block text-muted"><?php echo e($o->items_count); ?> <?php echo e(\Illuminate\Support\Str::plural('course', $o->items_count)); ?></small></td>
                            <td><span class="adm-badge adm-badge--<?php echo e($o->payment_status); ?>"><?php echo e(CourseOrder::PAYMENT_STATUSES[$o->payment_status] ?? $o->payment_status); ?></span></td>
                            <td><span class="adm-badge adm-badge--<?php echo e($o->status); ?>"><?php echo e(CourseOrder::STATUSES[$o->status] ?? $o->status); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="adm-pager">
            <span>Showing <?php echo e($orders->firstItem()); ?>–<?php echo e($orders->lastItem()); ?> of <?php echo e($orders->total()); ?> orders</span>
            <?php echo e($orders->links('pagination::bootstrap-4')); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/reports/orderReports.blade.php ENDPATH**/ ?>