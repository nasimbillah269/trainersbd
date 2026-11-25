<?php $__env->startSection('title', 'Student Login | TrainersBD'); ?>
<?php $__env->startSection('body_class', 'page-account'); ?>
<?php $__env->startPush('css'); ?><meta name="robots" content="noindex, nofollow"><?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="section account-auth">
    <div class="container">
        <div class="account-auth__card panel">
            <span class="account-auth__icon"><i class="bi bi-mortarboard"></i></span>
            <h1 class="account-auth__title">Student login</h1>
            <p class="account-auth__lead">Log in to see the courses you've bought and watch your lessons.</p>

            <form method="post" action="<?php echo e(route('customer.login.post')); ?>" novalidate>
                <?php echo csrf_field(); ?>
                <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle"></i> <?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="mb-3">
                    <label class="form-label" for="loginId">Mobile number or email</label>
                    <div class="input-icon"><i class="bi bi-person"></i>
                        <input type="text" class="form-control" id="loginId" name="login" value="<?php echo e(old('login')); ?>" placeholder="01XXXXXXXXX" required autocomplete="username" autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="loginPass">Password</label>
                    <div class="input-icon"><i class="bi bi-key"></i>
                        <input type="password" class="form-control" id="loginPass" name="password" required autocomplete="current-password">
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="loginRemember" name="remember" value="1">
                    <label class="form-check-label" for="loginRemember">Keep me logged in</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right"></i> Log in</button>
            </form>

            <div class="account-auth__foot">
                <p>Don't have an account yet? It's created automatically when you buy your first course at checkout.</p>
                <p>Forgot your password? Contact us and we'll help you get back in.</p>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/account/login.blade.php ENDPATH**/ ?>