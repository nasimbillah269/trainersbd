<?php $__env->startSection('title'); ?><title>Reviews | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li><a href="<?php echo e(route('admin.courses.index')); ?>">Courses</a></li><li>Reviews</li></ul>
        <h1>Reviews &amp; ratings</h1>
        <p>Approve student reviews before they appear on course pages.</p>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="adm-toolbar">
    <nav class="adm-tabs">
        <a href="<?php echo e(route('admin.course-reviews.index')); ?>" class="<?php echo e(!$status ? 'is-active' : ''); ?>">All <em><?php echo e($counts->sum()); ?></em></a>
        <a href="<?php echo e(route('admin.course-reviews.index', ['status' => 'pending'])); ?>" class="<?php echo e($status === 'pending' ? 'is-active' : ''); ?>">Pending <em><?php echo e($counts['pending'] ?? 0); ?></em></a>
        <a href="<?php echo e(route('admin.course-reviews.index', ['status' => 'approved'])); ?>" class="<?php echo e($status === 'approved' ? 'is-active' : ''); ?>">Approved <em><?php echo e($counts['approved'] ?? 0); ?></em></a>
        <a href="<?php echo e(route('admin.course-reviews.index', ['status' => 'rejected'])); ?>" class="<?php echo e($status === 'rejected' ? 'is-active' : ''); ?>">Rejected <em><?php echo e($counts['rejected'] ?? 0); ?></em></a>
    </nav>
    <form method="get">
        <?php if($status): ?><input type="hidden" name="status" value="<?php echo e($status); ?>"><?php endif; ?>
        <div class="adm-search" style="width:260px"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name or text…" style="height:36px"></div>
    </form>
</div>

<div class="adm-card">
    <?php if($reviews->isEmpty()): ?>
        <div class="adm-empty"><i class="fa-regular fa-star"></i><h5>No reviews here</h5><p>Reviews submitted on course pages will appear in this list.</p></div>
    <?php else: ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead><tr><th>Student</th><th>Review</th><th>Course</th><th>Status</th><th class="actions">Actions</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="min-width:200px"><div class="adm-cell"><span class="adm-avatar"><?php echo e($rv->initials()); ?></span><div><strong><?php echo e($rv->name); ?></strong><small><?php echo e($rv->email ?: '—'); ?></small><small><?php echo e($rv->created_at->format('d M Y, h:i A')); ?></small></div></div></td>
                            <td style="min-width:300px">
                                <div style="color:#F79009;font-size:.8rem;letter-spacing:1px"><?php for($s = 1; $s <= 5; $s++): ?><i class="fa-<?php echo e($s <= $rv->rating ? 'solid' : 'regular'); ?> fa-star"></i><?php endfor; ?></div>
                                <div class="mt-1"><?php echo e($rv->review); ?></div>
                            </td>
                            <td><?php if($rv->course): ?><a href="<?php echo e($rv->course->url()); ?>#reviews" target="_blank"><?php echo e(\Illuminate\Support\Str::limit($rv->course->title, 40)); ?></a><?php else: ?> — <?php endif; ?></td>
                            <td><span class="adm-badge adm-badge--<?php echo e($rv->status); ?>"><?php echo e(ucfirst($rv->status)); ?></span></td>
                            <td class="actions">
                                <?php if($rv->status !== 'approved'): ?>
                                    <form method="post" action="<?php echo e(route('admin.course-reviews.status', $rv)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="status" value="approved"><button class="adm-btn adm-btn--sm adm-btn--success"><i class="fa-solid fa-check"></i> Approve</button></form>
                                <?php endif; ?>
                                <?php if($rv->status !== 'rejected'): ?>
                                    <form method="post" action="<?php echo e(route('admin.course-reviews.status', $rv)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="status" value="rejected"><button class="adm-btn adm-btn--sm adm-btn--warning"><i class="fa-solid fa-ban"></i> Reject</button></form>
                                <?php endif; ?>
                                <form method="post" action="<?php echo e(route('admin.course-reviews.destroy', $rv)); ?>"><?php echo csrf_field(); ?><button class="adm-btn adm-btn--sm adm-btn--icon adm-btn--danger" title="Delete" data-confirm="Delete this review permanently?"><i class="fa-regular fa-trash-can"></i></button></form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="adm-pager"><span><?php echo e($reviews->total()); ?> reviews</span><?php echo e($reviews->links('pagination::bootstrap-4')); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/courses/reviews.blade.php ENDPATH**/ ?>