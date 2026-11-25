<?php $__env->startSection('title', $code . ' | TrainersBD'); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="container">
        <div class="empty-state">
            <span class="empty-state__icon"><i class="bi <?php echo e($icon ?? 'bi-exclamation-circle'); ?>"></i></span>
            <h2><?php echo e($code); ?> &middot; <?php echo e($title); ?></h2>
            <p><?php echo e($message); ?></p>
            <a href="<?php echo e(route('index')); ?>" class="btn btn-primary btn-lg"><i class="bi bi-house"></i> Back to Home</a>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/errors/layout.blade.php ENDPATH**/ ?>