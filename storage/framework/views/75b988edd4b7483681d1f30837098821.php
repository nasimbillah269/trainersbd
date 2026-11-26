<?php ($site = \App\Support\Storefront::site()); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', $site['name'] . ' | ' . $site['tagline']); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', $site['meta_description']); ?>">
    <meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', $site['meta_keywords']); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', $site['name']); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', $site['tagline']); ?>">
    <meta name="theme-color" content="#003080">
    <link rel="icon" type="image/png" href="<?php echo e(asset(general()->favicon())); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset(general()->favicon())); ?>">
    <meta property="og:image" content="<?php echo e(asset('frontend/images/brand/trainersbd-banner.png')); ?>">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/css/app.css')); ?>?v=21">
    <?php echo $__env->yieldPushContent('css'); ?>
</head>
<body class="<?php echo $__env->yieldContent('body_class'); ?>">

    <?php echo $__env->make('frontend.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <main id="main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('frontend.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <div class="modal fade video-modal" id="videoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <span class="video-modal__eyebrow">Now playing</span>
                        <h5 class="modal-title" id="videoModalTitle">Lesson</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9" id="videoModalFrame"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-stack" id="toastStack" aria-live="polite"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js"></script>
    <script>
        window.TBD = {
            routes: {
                cart: <?php echo json_encode(route('cart'), 15, 512) ?>,
                checkout: <?php echo json_encode(route('checkout'), 15, 512) ?>,
                checkoutPlace: <?php echo json_encode(route('checkout.place'), 15, 512) ?>,
                cartSync: <?php echo json_encode(route('cart.sync'), 15, 512) ?>,
                home: <?php echo json_encode(route('index'), 15, 512) ?>,
            },
            csrf: <?php echo json_encode(csrf_token(), 15, 512) ?>
        };
    </script>
    <script src="<?php echo e(asset('frontend/js/app.js')); ?>?v=6"></script>
    <?php echo $__env->yieldPushContent('js'); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/layouts/app.blade.php ENDPATH**/ ?>