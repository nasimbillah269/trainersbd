<?php
    use App\Support\Storefront;
    $freeSection = $freeSection ?? false;
    $listRoute = $freeSection ? 'admin.free-courses.index' : 'admin.courses.index';
    $addUrl = $freeSection ? route('admin.courses.create', ['type' => 'free']) : route('admin.courses.create');
?>
<?php $__env->startSection('title'); ?><title><?php echo e($freeSection ? 'Free Courses' : 'Courses'); ?> | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li><?php echo e($freeSection ? 'Free Courses' : 'Courses'); ?></li></ul>
        <?php if($freeSection): ?>
            <h1><i class="fa-brands fa-youtube" style="color:#FF0000"></i> Free Courses</h1>
            <p>YouTube courses students can watch on the website without payment or sign-up.</p>
        <?php else: ?>
            <h1>Courses</h1>
            <p>Create, publish and organise your paid and free courses.</p>
        <?php endif; ?>
    </div>
    <div class="adm-page-head__actions">
        <?php if($freeSection): ?>
            <a href="<?php echo e(route('courses.free')); ?>" target="_blank" class="adm-btn"><i class="fa-regular fa-eye"></i> View on website</a>
        <?php else: ?>
            <a href="<?php echo e(route('admin.course-categories.index')); ?>" class="adm-btn"><i class="fa-solid fa-shapes"></i> Categories</a>
        <?php endif; ?>
        <a href="<?php echo e($addUrl); ?>" class="adm-btn adm-btn--primary"><i class="fa-solid fa-plus"></i> <?php echo e($freeSection ? 'Add Free Course' : 'Add Course'); ?></a>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php if($freeSection): ?>
    <div class="adm-mini-stats" style="grid-template-columns:repeat(4,minmax(0,1fr))">
        <div class="adm-mini" style="--mi:#FF0000;--mi-bg:#FEF3F2"><span class="adm-mini__icon"><i class="fa-brands fa-youtube"></i></span><div><strong><?php echo e($counts['all']); ?></strong><small>Free courses</small></div></div>
        <div class="adm-mini" style="--mi:#079455;--mi-bg:#ECFDF3"><span class="adm-mini__icon"><i class="fa-solid fa-globe"></i></span><div><strong><?php echo e($counts['active']); ?></strong><small>Published</small></div></div>
        <div class="adm-mini"><span class="adm-mini__icon"><i class="fa-solid fa-circle-play"></i></span><div><strong><?php echo e($counts['lessons']); ?></strong><small>Video lessons</small></div></div>
        <div class="adm-mini" style="--mi:#DC6803;--mi-bg:#FFFAEB"><span class="adm-mini__icon"><i class="fa-solid fa-users"></i></span><div><strong><?php echo e(number_format($counts['students'])); ?></strong><small>Learners (display)</small></div></div>
    </div>
<?php endif; ?>

<div class="adm-toolbar">
    <nav class="adm-tabs">
        <a href="<?php echo e(route($listRoute)); ?>" class="<?php echo e(!request('status') && !request('type') ? 'is-active' : ''); ?>">All <em><?php echo e($counts['all']); ?></em></a>
        <a href="<?php echo e(route($listRoute, ['status' => 'active'])); ?>" class="<?php echo e(request('status') === 'active' ? 'is-active' : ''); ?>">Published <em><?php echo e($counts['active']); ?></em></a>
        <a href="<?php echo e(route($listRoute, ['status' => 'draft'])); ?>" class="<?php echo e(request('status') === 'draft' ? 'is-active' : ''); ?>">Drafts <em><?php echo e($counts['draft']); ?></em></a>
        <?php if (! ($freeSection)): ?>
            <a href="<?php echo e(route('admin.courses.index', ['type' => 'paid'])); ?>" class="<?php echo e(request('type') === 'paid' ? 'is-active' : ''); ?>">Paid <em><?php echo e($counts['paid']); ?></em></a>
            <a href="<?php echo e(route('admin.free-courses.index')); ?>">Free <em><?php echo e($counts['free']); ?></em></a>
        <?php endif; ?>
    </nav>
    <form method="get" action="<?php echo e(route($listRoute)); ?>">
        <?php $__currentLoopData = ['status', 'type']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(request($keep)): ?><input type="hidden" name="<?php echo e($keep); ?>" value="<?php echo e(request($keep)); ?>"><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <select name="category" class="adm-select adm-select--sm" style="width:190px" onchange="this.form.submit()">
            <option value="">All categories</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php if(request('category') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <div class="adm-search" style="width:240px">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search courses…" style="height:36px">
        </div>
    </form>
</div>

<div class="adm-card">
    <?php if($courses->isEmpty()): ?>
        <div class="adm-empty">
            <i class="fa-solid fa-layer-group"></i>
            <h5>No <?php echo e($freeSection ? 'free ' : ''); ?>courses found</h5>
            <p>Try a different filter, or add your first <?php echo e($freeSection ? 'free ' : ''); ?>course.</p>
            <a href="<?php echo e($addUrl); ?>" class="adm-btn adm-btn--primary mt-2"><i class="fa-solid fa-plus"></i> <?php echo e($freeSection ? 'Add Free Course' : 'Add Course'); ?></a>
        </div>
    <?php else: ?>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Category</th>
                        <?php if (! ($freeSection)): ?><th class="num">Price</th><?php endif; ?>
                        <th class="num">Lessons</th>
                        <?php if (! ($freeSection)): ?><th class="num">Sales</th><?php endif; ?>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="min-width:320px">
                                <div class="adm-cell">
                                    <?php if($course->thumbnailUrl()): ?>
                                        <img src="<?php echo e($course->thumbnailUrl()); ?>" class="adm-thumb" alt="">
                                    <?php else: ?>
                                        <span class="adm-thumb"><i class="fa-regular fa-image"></i></span>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('admin.courses.edit', $course)); ?>">
                                        <strong><?php echo e($course->title); ?></strong>
                                        <small><?php echo e($course->instructor->name ?? 'No instructor'); ?> &middot; Updated <?php echo e($course->updated_at->diffForHumans()); ?></small>
                                    </a>
                                </div>
                            </td>
                            <td><?php echo e($course->category->name ?? '—'); ?></td>
                            <?php if (! ($freeSection)): ?><td class="num">
                                <?php if($course->isFree()): ?>
                                    <span class="adm-badge adm-badge--free">Free</span>
                                <?php else: ?>
                                    <b><?php echo e(Storefront::money($course->price)); ?></b>
                                    <?php if($course->discountPercent()): ?><small class="d-block text-muted"><s><?php echo e(Storefront::money($course->old_price)); ?></s> &middot; <?php echo e($course->discountPercent()); ?>% off</small><?php endif; ?>
                                <?php endif; ?>
                            </td><?php endif; ?>
                            <td class="num"><?php echo e($course->lessons_count); ?></td>
                            <?php if (! ($freeSection)): ?><td class="num"><?php echo e($course->sales_count); ?></td><?php endif; ?>
                            <td>
                                <?php if($course->reviews_count): ?>
                                    <span style="color:#B54708;font-weight:700"><i class="fa-solid fa-star" style="color:#F79009"></i> <?php echo e(number_format($course->rating_avg, 1)); ?></span>
                                    <small class="text-muted">(<?php echo e($course->reviews_count); ?>)</small>
                                <?php else: ?>
                                    <small class="text-muted">No reviews</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <label class="adm-switch" title="Published">
                                    <input type="checkbox" data-quick="<?php echo e(route('admin.courses.quick', $course)); ?>" data-field="status" <?php if($course->status === 'active'): echo 'checked'; endif; ?>>
                                    <span class="adm-switch__ui"></span>
                                    <span class="adm-badge adm-badge--<?php echo e($course->status); ?>" data-status-label><?php echo e($course->status === 'active' ? 'Published' : 'Draft'); ?></span>
                                </label>
                            </td>
                            <td>
                                <label class="adm-switch" title="Featured on home page">
                                    <input type="checkbox" data-quick="<?php echo e(route('admin.courses.quick', $course)); ?>" data-field="featured" <?php if($course->featured): echo 'checked'; endif; ?>>
                                    <span class="adm-switch__ui"></span>
                                </label>
                            </td>
                            <td class="actions">
                                <a href="<?php echo e($course->url()); ?>" target="_blank" class="adm-btn adm-btn--sm adm-btn--icon" title="View on website"><i class="fa-regular fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.courses.edit', $course)); ?>" class="adm-btn adm-btn--sm adm-btn--icon" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                                <form method="post" action="<?php echo e(route('admin.courses.duplicate', $course)); ?>"><?php echo csrf_field(); ?><button class="adm-btn adm-btn--sm adm-btn--icon" title="Duplicate"><i class="fa-regular fa-copy"></i></button></form>
                                <form method="post" action="<?php echo e(route('admin.courses.destroy', $course)); ?>"><?php echo csrf_field(); ?><button class="adm-btn adm-btn--sm adm-btn--icon adm-btn--danger" title="Delete" data-confirm="Delete “<?php echo e($course->title); ?>”? Its modules, lessons and reviews will be removed too."><i class="fa-regular fa-trash-can"></i></button></form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="adm-pager">
            <span>Showing <?php echo e($courses->firstItem()); ?>–<?php echo e($courses->lastItem()); ?> of <?php echo e($courses->total()); ?> courses</span>
            <?php echo e($courses->links('pagination::bootstrap-4')); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script>
    $(document).on('change', '[data-quick]', function () {
        var input = this, field = input.dataset.field;
        var value = field === 'status' ? (input.checked ? 'active' : 'draft') : (input.checked ? 1 : 0);
        $.post(input.dataset.quick, { field: field, value: value }).done(function () {
            if (field === 'status') {
                var label = $(input).closest('label').find('[data-status-label]');
                label.text(input.checked ? 'Published' : 'Draft').attr('class', 'adm-badge adm-badge--' + (input.checked ? 'active' : 'draft'));
            }
        }).fail(function () { input.checked = !input.checked; alert('Could not update. Please try again.'); });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/courses/index.blade.php ENDPATH**/ ?>