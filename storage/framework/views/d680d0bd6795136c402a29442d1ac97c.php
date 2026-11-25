
<?php if($errors->any()): ?>
    <div class="adm-alert adm-alert--danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div>
            <strong>Please fix the following:</strong>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/alerts.blade.php ENDPATH**/ ?>