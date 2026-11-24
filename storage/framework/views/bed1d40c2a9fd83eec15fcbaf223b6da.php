<ol class="checkout-steps" aria-label="Checkout progress">
    <li class="<?php echo e($step >= 1 ? 'is-done' : ''); ?> <?php echo e($step === 1 ? 'is-current' : ''); ?>"><span>1</span> Cart</li>
    <li class="<?php echo e($step >= 2 ? 'is-done' : ''); ?> <?php echo e($step === 2 ? 'is-current' : ''); ?>"><span>2</span> Checkout</li>
    <li class="<?php echo e($step >= 3 ? 'is-done' : ''); ?> <?php echo e($step === 3 ? 'is-current' : ''); ?>"><span>3</span> Confirmation</li>
</ol>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/partials/checkout-steps.blade.php ENDPATH**/ ?>