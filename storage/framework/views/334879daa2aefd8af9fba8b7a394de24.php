
<?php
    $presets = ['today' => 'Today', '7d' => '7 days', '30d' => '30 days', 'month' => 'This month', 'year' => 'This year', 'all' => 'All time'];
    $keep = request()->except(['range', 'startDate', 'endDate', 'page', 'export']);
    $current = request('range', (request('startDate') || request('endDate')) ? 'custom' : $defaultRange);
?>
<div class="adm-toolbar">
    <nav class="adm-tabs">
        <?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('admin.reportsAll', array_merge(['type' => $type], $keep, ['range' => $key]))); ?>" class="<?php echo e($current === $key ? 'is-active' : ''); ?>"><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
    <form method="get" action="<?php echo e(route('admin.reportsAll', $type)); ?>" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
        <?php $__currentLoopData = $keep; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!is_array($v)): ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <input type="date" name="startDate" value="<?php echo e($from?->format('Y-m-d')); ?>" class="adm-input adm-input--sm" style="width:150px" title="From date">
        <span class="text-muted">to</span>
        <input type="date" name="endDate" value="<?php echo e($to?->format('Y-m-d')); ?>" class="adm-input adm-input--sm" style="width:150px" title="To date">
        <button class="adm-btn adm-btn--sm adm-btn--primary"><i class="fa-solid fa-filter"></i> Apply</button>
    </form>
</div>
<p class="text-muted" style="margin:-.5rem 0 1rem;font-size:.82rem">
    <i class="fa-regular fa-calendar"></i>
    <?php if($from || $to): ?>
        Showing <?php echo e($from ? $from->format('d M Y') : 'beginning'); ?> &ndash; <?php echo e($to ? $to->format('d M Y') : 'today'); ?>

    <?php else: ?>
        Showing all time
    <?php endif; ?>
</p>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/reports/_range.blade.php ENDPATH**/ ?>