<?php $__env->startSection('title'); ?><title>Orders | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>
<?php use App\Support\Storefront; use App\Models\CourseOrder; ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li>Orders</li></ul>
        <h1>Orders</h1>
        <p>Verify manual payments and confirm, hold or cancel orders.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="<?php echo e(route('admin.course-orders.index', ['payment' => 'unverified'])); ?>" class="adm-btn <?php echo e(request('payment') === 'unverified' ? 'adm-btn--primary' : ''); ?>"><i class="fa-solid fa-shield-halved"></i> Unverified payments</a>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="adm-toolbar">
    <nav class="adm-tabs">
        <a href="<?php echo e(route('admin.course-orders.index')); ?>" class="<?php echo e(!$status && !request('payment') ? 'is-active' : ''); ?>">All <em><?php echo e($counts['all'] ?? 0); ?></em></a>
        <?php $__currentLoopData = CourseOrder::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('admin.course-orders.index', ['status' => $key])); ?>" class="<?php echo e($status === $key ? 'is-active' : ''); ?>"><?php echo e($label); ?> <em><?php echo e($counts[$key] ?? 0); ?></em></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
    <form method="get">
        <?php if($status): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
        <select name="method" class="adm-select adm-select--sm" style="width:150px" onchange="this.form.submit()">
            <option value="">All methods</option>
            <option value="bkash" <?php if(request('method') === 'bkash'): echo 'selected'; endif; ?>>bKash</option>
            <option value="nagad" <?php if(request('method') === 'nagad'): echo 'selected'; endif; ?>>Nagad</option>
            <option value="bank" <?php if(request('method') === 'bank'): echo 'selected'; endif; ?>>Bank</option>
        </select>
        <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="adm-input adm-input--sm" style="width:150px" title="From date" onchange="this.form.submit()">
        <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="adm-input adm-input--sm" style="width:150px" title="To date" onchange="this.form.submit()">
        <div class="adm-search" style="width:240px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Invoice, name, mobile, TrxID" style="height:36px"></div>
        <?php if(request()->hasAny(['q', 'method', 'from', 'to', 'payment'])): ?><a href="<?php echo e(route('admin.course-orders.index', array_filter(['status' => $status]))); ?>" class="adm-btn adm-btn--sm">Clear</a><?php endif; ?>
    </form>
</div>

<div class="adm-card">
    <?php if($orders->isEmpty()): ?>
        <div class="adm-empty"><i class="fa-solid fa-receipt"></i><h5>No orders found</h5><p>Orders placed from the website checkout will appear here.</p></div>
    <?php else: ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr><th>Order</th><th>Student</th><th>Payment details</th><th class="num">Amount</th><th>Payment</th><th>Status</th><th class="actions">Actions</th></tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><a href="<?php echo e(route('admin.course-orders.show', $o)); ?>" class="adm-mono"><b>#<?php echo e($o->invoice); ?></b></a><small class="d-block text-muted"><?php echo e($o->created_at->format('d M Y, h:i A')); ?></small></td>
                            <td><div class="adm-cell"><div><strong><?php echo e($o->name); ?></strong><small><a href="tel:<?php echo e($o->mobile); ?>"><?php echo e($o->mobile); ?></a></small><small><?php echo e($o->email); ?></small></div></div></td>
                            <td>
                                <span class="adm-badge adm-badge--plain adm-badge--<?php echo e($o->payment_method); ?>"><?php echo e($o->methodName()); ?></span>
                                <small class="d-block mt-1">TrxID: <b class="adm-mono text-dark"><?php echo e($o->transaction_id); ?></b></small>
                                <small class="d-block text-muted">From: <span class="adm-mono"><?php echo e($o->sender_number); ?></span></small>
                            </td>
                            <td class="num"><b><?php echo e(Storefront::money($o->total)); ?></b><small class="d-block text-muted"><?php echo e($o->items_count); ?> <?php echo e(\Illuminate\Support\Str::plural('course', $o->items_count)); ?></small></td>
                            <td><span class="adm-badge adm-badge--<?php echo e($o->payment_status); ?>"><?php echo e(CourseOrder::PAYMENT_STATUSES[$o->payment_status]); ?></span></td>
                            <td><span class="adm-badge adm-badge--<?php echo e($o->status); ?>"><?php echo e(CourseOrder::STATUSES[$o->status]); ?></span></td>
                            <td class="actions">
                                <?php if($o->status !== 'confirmed'): ?>
                                    <form method="post" action="<?php echo e(route('admin.course-orders.quick', $o)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="confirm"><button class="adm-btn adm-btn--sm adm-btn--success" title="Payment verified, confirm order" data-confirm="Confirm order #<?php echo e($o->invoice); ?>? This marks the payment (<?php echo e($o->transaction_id); ?>) as verified."><i class="fa-solid fa-check"></i> Confirm</button></form>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admin.course-orders.show', $o)); ?>" class="adm-btn adm-btn--sm adm-btn--icon" title="Open"><i class="fa-solid fa-arrow-right"></i></a>
                            </td>
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

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/course-orders/index.blade.php ENDPATH**/ ?>