<?php $__env->startSection('title'); ?><title>Students | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>
<?php use App\Support\Storefront; ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li>Students</li></ul>
        <h1>Students</h1>
        <p>Customer list built from orders (name, mobile and email). Students don't need an account.</p>
    </div>
    <div class="adm-page-head__actions">
        <a href="<?php echo e(route('admin.students.export')); ?>" class="adm-btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
    </div>
</div>

<div class="adm-toolbar">
    <span class="text-muted"><?php echo e($students->total()); ?> unique students</span>
    <form method="get">
        <div class="adm-search" style="width:280px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name, mobile or email" style="height:36px"></div>
    </form>
</div>

<div class="adm-card">
    <?php if($students->isEmpty()): ?>
        <div class="adm-empty"><i class="fa-solid fa-user-graduate"></i><h5>No students yet</h5><p>Students appear here after their first order.</p></div>
    <?php else: ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Student</th><th>Mobile</th><th class="num">Orders</th><th class="num">Confirmed</th><th class="num">Spent</th><th>Last order</th><th class="actions"></th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><div class="adm-cell"><span class="adm-avatar"><?php echo e(Storefront::initials($s->name)); ?></span><div><strong><?php echo e($s->name); ?></strong><small><?php echo e($s->email); ?></small></div></div></td>
                            <td class="adm-mono"><a href="tel:<?php echo e($s->mobile); ?>"><?php echo e($s->mobile); ?></a></td>
                            <td class="num"><?php echo e($s->orders); ?></td>
                            <td class="num"><?php echo e((int) $s->confirmed); ?></td>
                            <td class="num"><b><?php echo e(Storefront::money($s->spent)); ?></b></td>
                            <td><?php echo e(\Carbon\Carbon::parse($s->last_order)->format('d M Y')); ?></td>
                            <td class="actions"><a href="<?php echo e(route('admin.course-orders.index', ['q' => $s->mobile])); ?>" class="adm-btn adm-btn--sm">Orders <i class="fa-solid fa-arrow-right"></i></a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="adm-pager"><span>Page <?php echo e($students->currentPage()); ?> of <?php echo e($students->lastPage()); ?></span><?php echo e($students->links('pagination::bootstrap-4')); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/course-orders/students.blade.php ENDPATH**/ ?>