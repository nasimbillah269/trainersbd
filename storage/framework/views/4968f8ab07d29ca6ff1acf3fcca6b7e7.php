<?php $__env->startSection('title'); ?><title>Customer Report | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>
<?php use App\Support\Storefront; ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li>Reports</li><li>Customers</li></ul>
        <h1>Customer Report</h1>
        <p>Students who placed course orders, grouped by mobile number.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => $type], request()->except('page'), ['export' => 'csv']))); ?>" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="<?php echo e(route('admin.reportsAll', $type)); ?>" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('admin.reports._range', ['defaultRange' => 'all'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="adm-stats">
    <div class="adm-stat"><div class="adm-stat__icon"><i class="fa-solid fa-users"></i></div><div class="adm-stat__label">Customers</div><div class="adm-stat__value"><?php echo e(number_format($totals['customers'])); ?></div><div class="adm-stat__meta">Placed at least one order</div></div>
    <div class="adm-stat adm-stat--ok"><div class="adm-stat__icon"><i class="fa-solid fa-user-graduate"></i></div><div class="adm-stat__label">Paying students</div><div class="adm-stat__value"><?php echo e(number_format($totals['buyers'])); ?></div><div class="adm-stat__meta">With a confirmed order</div></div>
    <div class="adm-stat adm-stat--hero"><div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div><div class="adm-stat__label">Total spent (confirmed)</div><div class="adm-stat__value"><?php echo e(Storefront::money($totals['spent'])); ?></div></div>
    <div class="adm-stat adm-stat--warn"><div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div><div class="adm-stat__label">Due (pending / hold)</div><div class="adm-stat__value"><?php echo e(Storefront::money($totals['due'])); ?></div></div>
</div>

<div class="adm-card">
    <div class="adm-card__head">
        <form method="get" action="<?php echo e(route('admin.reportsAll', $type)); ?>" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%">
            <?php $__currentLoopData = request()->only(['range', 'startDate', 'endDate']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <select name="buyer" class="adm-select adm-select--sm" style="width:190px" onchange="this.form.submit()">
                <option value="">All customers</option>
                <option value="confirmed" <?php if(request('buyer') === 'confirmed'): echo 'selected'; endif; ?>>With confirmed order</option>
                <option value="none" <?php if(request('buyer') === 'none'): echo 'selected'; endif; ?>>No confirmed order</option>
            </select>
            <select name="sort" class="adm-select adm-select--sm" style="width:170px" onchange="this.form.submit()">
                <option value="">Sort: last order</option>
                <option value="spent" <?php if(request('sort') === 'spent'): echo 'selected'; endif; ?>>Sort: spent</option>
                <option value="orders" <?php if(request('sort') === 'orders'): echo 'selected'; endif; ?>>Sort: orders</option>
            </select>
            <div class="adm-search" style="width:250px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Name, mobile, email" style="height:36px"></div>
            <?php if(request()->hasAny(['buyer', 'sort', 'search'])): ?><a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => $type], request()->only(['range', 'startDate', 'endDate'])))); ?>" class="adm-btn adm-btn--sm">Clear</a><?php endif; ?>
        </form>
    </div>
    <?php if($customers->isEmpty()): ?>
        <div class="adm-empty"><i class="fa-solid fa-users"></i><h5>No customers found</h5><p>Try a wider date range or fewer filters.</p></div>
    <?php else: ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Customer</th><th>Account</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Pending / Hold</th><th class="num">Due</th><th class="num">Spent</th><th>Last order</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong><?php echo e($c->name); ?></strong>
                                <small class="d-block"><a href="<?php echo e(route('admin.reportsAll', ['type' => 'order-reports', 'range' => 'all', 'search' => $c->mobile])); ?>" title="View this customer's orders"><?php echo e($c->mobile); ?></a></small>
                                <small class="d-block text-muted"><?php echo e($c->email); ?></small>
                            </td>
                            <td><?php if($c->user_id): ?><span class="adm-badge adm-badge--success">Student account</span><?php else: ?><span class="adm-badge adm-badge--muted">Guest</span><?php endif; ?></td>
                            <td class="num"><?php echo e($c->orders); ?></td>
                            <td class="num"><?php echo e((int) $c->confirmed); ?></td>
                            <td class="num"><?php echo e((int) $c->pending); ?></td>
                            <td class="num"><?php echo e(Storefront::money($c->due)); ?></td>
                            <td class="num"><b><?php echo e(Storefront::money($c->spent)); ?></b></td>
                            <td><?php echo e(\Carbon\Carbon::parse($c->last_order)->format('d M Y')); ?><small class="d-block text-muted">since <?php echo e(\Carbon\Carbon::parse($c->first_order)->format('d M Y')); ?></small></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="adm-pager">
            <span>Showing <?php echo e($customers->firstItem()); ?>–<?php echo e($customers->lastItem()); ?> of <?php echo e($customers->total()); ?> customers</span>
            <?php echo e($customers->links('pagination::bootstrap-4')); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/reports/customerReports.blade.php ENDPATH**/ ?>