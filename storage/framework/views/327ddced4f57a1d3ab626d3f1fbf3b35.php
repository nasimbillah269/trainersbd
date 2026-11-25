<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <?php if (! empty(trim($__env->yieldContent('title')))): ?>
        <?php echo $__env->yieldContent('title'); ?>
    <?php else: ?>
        <title>Admin | <?php echo e(general()->title); ?></title>
    <?php endif; ?>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset(general()->favicon())); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@500;600&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/vendors/css/vendors.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/vendors/css/forms/selects/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/css/bootstrap-extended.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/css/components.min.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/css/tag-editor.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/css/style.css')); ?>">
    <script src="<?php echo e(asset(assetLinkAdmin() . '/app-assets/js/JsBarcode.all.js')); ?>"></script>

    
    <link rel="stylesheet" href="<?php echo e(asset(assetLinkAdmin() . '/app-assets/css/premium.css')); ?>?v=4">
    <?php echo $__env->yieldPushContent('css'); ?>
</head>
<body class="adm">
    <div class="adm-shell">
        <?php echo $__env->make('admin.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="adm-backdrop" data-adm-close></div>

        <div class="adm-main">
            <?php echo $__env->make('admin.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <main class="adm-content">
                <div class="content-wrapper">
                    <?php echo $__env->yieldContent('contents'); ?>
                </div>
            </main>
            <?php echo $__env->make('admin.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>

    <div class="adm-toasts" id="admToasts">
        <?php $__currentLoopData = ['success' => 'fa-circle-check', 'error' => 'fa-circle-exclamation', 'info' => 'fa-circle-info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session($type)): ?>
                <div class="adm-toast adm-toast--<?php echo e($type); ?>"><i class="fa-solid <?php echo e($icon); ?>"></i><span><?php echo e(session($type)); ?></span><button type="button" data-toast-close aria-label="Close">&times;</button></div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <script src="<?php echo e(asset(assetLinkAdmin() . '/app-assets/vendors/js/vendors.min.js')); ?>"></script>
    <script src="<?php echo e(asset(assetLinkAdmin() . '/app-assets/vendors/js/forms/select/select2.full.min.js')); ?>"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="<?php echo e(asset(assetLinkAdmin() . '/app-assets/js/printThis.js')); ?>"></script>
    <script src="<?php echo e(asset(assetLinkAdmin() . '/app-assets/js/tag-editor.js')); ?>"></script>
    <script src="<?php echo e(asset(assetLinkAdmin() . '/app-assets/js/scripts/forms/select/form-select2.min.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="<?php echo e(asset('tinymce/tinymce.min.js')); ?>"></script>
    <script>
        (function () {
            // Sidebar toggle (mobile) and collapse (desktop)
            var body = document.body;
            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-adm-toggle]')) {
                    if (window.innerWidth < 1200) { body.classList.toggle('adm-nav-open'); }
                    else {
                        body.classList.toggle('adm-collapsed');
                        try { localStorage.setItem('admCollapsed', body.classList.contains('adm-collapsed') ? '1' : '0'); } catch (err) {}
                    }
                }
                if (e.target.closest('[data-adm-close]')) { body.classList.remove('adm-nav-open'); }
                var navToggle = e.target.closest('[data-nav-toggle]');
                if (navToggle) {
                    if (body.classList.contains('adm-collapsed')) { body.classList.remove('adm-collapsed'); try { localStorage.setItem('admCollapsed', '0'); } catch (err) {} }
                    var group = navToggle.closest('[data-nav-group]');
                    var open = group.classList.toggle('is-open');
                    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                }
                var close = e.target.closest('[data-toast-close]');
                if (close) { close.parentNode.remove(); }
                var confirmBtn = e.target.closest('[data-confirm]');
                if (confirmBtn && !window.confirm(confirmBtn.getAttribute('data-confirm'))) { e.preventDefault(); e.stopImmediatePropagation(); }
            }, true);
            try { if (localStorage.getItem('admCollapsed') === '1' && window.innerWidth >= 1200) { body.classList.add('adm-collapsed'); } } catch (err) {}
            setTimeout(function () {
                document.querySelectorAll('.adm-toast--success, .adm-toast--info').forEach(function (t) { t.classList.add('is-hiding'); setTimeout(function () { t.remove(); }, 400); });
            }, 5000);
        })();
    </script>
    <?php echo $__env->yieldPushContent('js'); ?>
    <?php echo $__env->make('admin.layouts.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>