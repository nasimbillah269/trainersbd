<?php
    $site = \App\Support\Storefront::site();
    $tel  = \App\Support\Storefront::tel();
    $socialIcons = ['facebook' => 'bi-facebook', 'youtube' => 'bi-youtube', 'linkedin' => 'bi-linkedin', 'instagram' => 'bi-instagram'];
?>
<footer class="site-footer" id="contact">
    <div class="container">
        <div class="footer-cta">
            <div>
                <h3>Not sure which course is right for you?</h3>
                <p>Call or message us and our team will help you choose, free of charge.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if($site['phone']): ?><a href="tel:<?php echo e($tel); ?>" class="btn btn-light btn-lg"><i class="bi bi-telephone"></i> Call Us</a><?php endif; ?>
                <?php if(isset($site['social']['facebook'])): ?><a href="<?php echo e($site['social']['facebook']); ?>" class="btn btn-outline-light btn-lg" target="_blank" rel="noopener"><i class="bi bi-messenger"></i> Message on Facebook</a><?php endif; ?>
            </div>
        </div>

        <div class="row g-5 footer-main">
            <div class="col-lg-4">
                <a class="brand brand--card" href="<?php echo e(route('index')); ?>">
                    <img src="<?php echo e(asset(general()->footer_logo ? assetPath(general()->footer_logo) : general()->logo())); ?>" alt="<?php echo e($site['name']); ?>" class="brand__logo" width="175" height="82">
                </a>
                <p class="footer-about"><?php echo e($site['meta_description'] ?: $site['tagline']); ?></p>
                <div class="footer-social">
                    <?php $__currentLoopData = $site['social']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $net => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($link); ?>" aria-label="<?php echo e(ucfirst($net)); ?>" target="_blank" rel="noopener"><i class="bi <?php echo e($socialIcons[$net]); ?>"></i></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Explore</h6>
                <ul>
                    <li><a href="<?php echo e(route('courses.index')); ?>">All Courses</a></li>
                    <li><a href="<?php echo e(route('courses.free')); ?>">Free Courses</a></li>
                    <li><a href="<?php echo e(route('index')); ?>#how-it-works">How to Enroll</a></li>
                    <li><a href="<?php echo e(route('cart')); ?>">My Cart</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Categories</h6>
                <ul>
                    <?php $__currentLoopData = \App\Support\Storefront::categories()->where('courses_count', '>', 0)->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e(route('courses.category', $cat->slug)); ?>"><?php echo e($cat->name); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>Contact</h6>
                <ul class="footer-contact">
                    <li><i class="bi bi-geo-alt"></i> <?php echo e($site['address']); ?></li>
                    <?php if($site['phone']): ?><li><i class="bi bi-telephone"></i> <a href="tel:<?php echo e($tel); ?>"><?php echo e($site['phone']); ?></a></li><?php endif; ?>
                    <?php if($site['email']): ?><li><i class="bi bi-envelope"></i> <a href="mailto:<?php echo e($site['email']); ?>"><?php echo e($site['email']); ?></a></li><?php endif; ?>
                </ul>
                <h6 class="mt-4">We accept</h6>
                <div class="pay-chips">
                    <?php $__currentLoopData = \App\Support\Storefront::paymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="pay-chip" style="--chip: <?php echo e($pm['key'] === 'bank' ? '#93C5FD' : $pm['color']); ?>"><?php if($pm['key'] === 'bank'): ?><i class="bi bi-bank"></i> <?php endif; ?><?php echo e($pm['name']); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?php echo e(date('Y')); ?> <?php echo e($site['name']); ?>. All rights reserved.</span>
            <span><a href="<?php echo e(url('privacy-policy')); ?>">Privacy Policy</a> &middot; <a href="<?php echo e(url('terms-and-conditions')); ?>">Terms</a> &middot; <a href="<?php echo e(url('refund-and-return-policy')); ?>">Refund Policy</a></span>
        </div>
    </div>
</footer>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/partials/footer.blade.php ENDPATH**/ ?>