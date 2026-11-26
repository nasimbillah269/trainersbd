<?php $__env->startSection('title'); ?><title>Course Report | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>
<?php use App\Support\Storefront; ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li>Reports</li><li>Courses</li></ul>
        <h1>Course Report</h1>
        <p>Orders and revenue per course. Revenue counts confirmed orders only.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => $type], request()->except('page'), ['export' => 'csv']))); ?>" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="<?php echo e(route('admin.reportsAll', $type)); ?>" class="adm-btn adm-btn--icon" title="Reset"><i class="fa-solid fa-rotate"></i></a>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('admin.reports._range', ['defaultRange' => 'all'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="adm-stats">
    <div class="adm-stat adm-stat--hero"><div class="adm-stat__icon"><i class="fa-solid fa-sack-dollar"></i></div><div class="adm-stat__label">Revenue</div><div class="adm-stat__value"><?php echo e(Storefront::money($courses->sum('r_revenue'))); ?></div></div>
    <div class="adm-stat adm-stat--warn"><div class="adm-stat__icon"><i class="fa-solid fa-hourglass-half"></i></div><div class="adm-stat__label">Due (pending / hold)</div><div class="adm-stat__value"><?php echo e(Storefront::money($courses->sum('r_due'))); ?></div></div>
    <div class="adm-stat adm-stat--info"><div class="adm-stat__icon"><i class="fa-solid fa-cart-shopping"></i></div><div class="adm-stat__label">Course enrolments ordered</div><div class="adm-stat__value"><?php echo e(number_format($courses->sum('r_orders'))); ?></div><div class="adm-stat__meta"><?php echo e(number_format($courses->sum('r_confirmed'))); ?> confirmed</div></div>
    <div class="adm-stat adm-stat--ok"><div class="adm-stat__icon"><i class="fa-solid fa-layer-group"></i></div><div class="adm-stat__label">Courses with sales</div><div class="adm-stat__value"><?php echo e($courses->where('r_orders', '>', 0)->count()); ?> / <?php echo e($courses->count()); ?></div></div>
</div>

<div class="adm-card">
    <div class="adm-card__head">
        <form method="get" action="<?php echo e(route('admin.reportsAll', $type)); ?>" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;width:100%">
            <?php $__currentLoopData = request()->only(['range', 'startDate', 'endDate']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <select name="category" class="adm-select adm-select--sm" style="width:190px" onchange="this.form.submit()">
                <option value="">All categories</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($cat->id); ?>" <?php if(request('category') == $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="course_type" class="adm-select adm-select--sm" style="width:130px" onchange="this.form.submit()">
                <option value="">Paid &amp; free</option>
                <option value="paid" <?php if(request('course_type') === 'paid'): echo 'selected'; endif; ?>>Paid</option>
                <option value="free" <?php if(request('course_type') === 'free'): echo 'selected'; endif; ?>>Free</option>
            </select>
            <select name="status" class="adm-select adm-select--sm" style="width:130px" onchange="this.form.submit()">
                <option value="">Any status</option>
                <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                <option value="draft" <?php if(request('status') === 'draft'): echo 'selected'; endif; ?>>Draft</option>
            </select>
            <select name="sort" class="adm-select adm-select--sm" style="width:160px" onchange="this.form.submit()">
                <option value="">Sort: revenue</option>
                <option value="orders" <?php if(request('sort') === 'orders'): echo 'selected'; endif; ?>>Sort: orders</option>
                <option value="title" <?php if(request('sort') === 'title'): echo 'selected'; endif; ?>>Sort: title</option>
            </select>
            <div class="adm-search" style="width:220px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Course title" style="height:36px"></div>
            <?php if(request()->hasAny(['category', 'course_type', 'status', 'sort', 'search'])): ?><a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => $type], request()->only(['range', 'startDate', 'endDate'])))); ?>" class="adm-btn adm-btn--sm">Clear</a><?php endif; ?>
        </form>
    </div>
    <?php if($courses->isEmpty()): ?>
        <div class="adm-empty"><i class="fa-solid fa-layer-group"></i><h5>No courses found</h5><p>Try changing the filters.</p></div>
    <?php else: ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>#</th><th>Course</th><th class="num">Price</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Pending / Hold</th><th class="num">Cancelled</th><th class="num">Due</th><th class="num">Revenue</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="<?php echo e($c->r_orders || $c->r_cancelled ? '' : 'opacity:.6'); ?>">
                            <td><?php echo e($i + 1); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.courses.edit', $c->id)); ?>"><strong><?php echo e($c->title); ?></strong></a>
                                <small class="d-block text-muted"><?php echo e($c->category->name ?? 'Uncategorized'); ?> &middot; <?php echo e(ucfirst($c->type)); ?><?php if($c->status !== 'active'): ?> &middot; <span class="adm-badge adm-badge--draft">Draft</span><?php endif; ?></small>
                            </td>
                            <td class="num"><?php echo e($c->type === 'free' ? 'Free' : Storefront::money($c->price)); ?></td>
                            <td class="num"><?php echo e((int) $c->r_orders); ?></td>
                            <td class="num"><?php echo e((int) $c->r_confirmed); ?></td>
                            <td class="num"><?php echo e((int) $c->r_pending); ?></td>
                            <td class="num"><?php echo e((int) $c->r_cancelled); ?></td>
                            <td class="num"><?php echo e(Storefront::money($c->r_due)); ?></td>
                            <td class="num"><b><?php echo e(Storefront::money($c->r_revenue)); ?></b></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr><th></th><th>Total</th><th></th><th class="num"><?php echo e((int) $courses->sum('r_orders')); ?></th><th class="num"><?php echo e((int) $courses->sum('r_confirmed')); ?></th><th class="num"><?php echo e((int) $courses->sum('r_pending')); ?></th><th class="num"><?php echo e((int) $courses->sum('r_cancelled')); ?></th><th class="num"><?php echo e(Storefront::money($courses->sum('r_due'))); ?></th><th class="num"><?php echo e(Storefront::money($courses->sum('r_revenue'))); ?></th></tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/reports/productReports.blade.php ENDPATH**/ ?>