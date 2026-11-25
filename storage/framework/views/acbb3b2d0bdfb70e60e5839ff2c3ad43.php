<?php
    use App\Support\Storefront;
    use App\Models\CourseOrder;
    $firstName = \Illuminate\Support\Str::before($user->name, ' ') ?: $user->name;
?>

<?php $__env->startSection('title', 'My Courses | TrainersBD'); ?>
<?php $__env->startSection('body_class', 'page-account'); ?>
<?php $__env->startPush('css'); ?><meta name="robots" content="noindex, nofollow"><?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('index')); ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Courses</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <h1 class="page-head__title">Hi, <?php echo e($firstName); ?> 👋</h1>
                <p class="text-muted mb-0"><?php echo e($user->mobile); ?> <?php if($user->email): ?>&middot; <?php echo e($user->email); ?><?php endif; ?></p>
            </div>
            <form method="post" action="<?php echo e(route('customer.logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-right"></i> Log out</button>
            </form>
        </div>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-warning"><i class="bi bi-info-circle"></i> <?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <div class="account-stats">
            <div class="account-stat"><i class="bi bi-mortarboard"></i><div><strong><?php echo e($stats['enrolled']); ?></strong><span>Enrolled courses</span></div></div>
            <div class="account-stat"><i class="bi bi-hourglass-split"></i><div><strong><?php echo e($stats['pending']); ?></strong><span>Awaiting verification</span></div></div>
            <div class="account-stat"><i class="bi bi-receipt"></i><div><strong><?php echo e($stats['orders']); ?></strong><span>Orders</span></div></div>
        </div>

        <h2 class="block-title mt-5 mb-3">My courses</h2>
        <?php if($courses->isEmpty()): ?>
            <div class="empty-state">
                <span class="empty-state__icon"><i class="bi bi-journal-bookmark"></i></span>
                <h2>No courses yet</h2>
                <p>Courses you buy will show up here.</p>
                <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-primary btn-lg">Browse Courses</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($course = $row['course']); ?>
                    <div class="col-sm-6 col-lg-4">
                        <article class="my-course <?php echo e($row['enrolled'] ? '' : 'is-pending'); ?>">
                            <a href="<?php echo e($row['enrolled'] ? route('customer.learn', $course->slug) : $course->url()); ?>" class="course-thumb <?php echo e($course->thumbnailUrl() ? 'course-thumb--img' : ''); ?>">
                                <?php if($course->thumbnailUrl()): ?>
                                    <img src="<?php echo e($course->thumbnailUrl()); ?>" alt="" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <i class="bi <?php echo e($course->icon()); ?> course-thumb__icon"></i>
                                <?php endif; ?>
                            </a>
                            <div class="my-course__body">
                                <?php if($row['enrolled']): ?>
                                    <span class="my-course__status is-ok"><i class="bi bi-check-circle-fill"></i> Enrolled</span>
                                <?php else: ?>
                                    <span class="my-course__status"><i class="bi bi-hourglass-split"></i> <?php echo e($row['order']->status === 'hold' ? 'On hold' : 'Payment being verified'); ?></span>
                                <?php endif; ?>
                                <h3><a href="<?php echo e($row['enrolled'] ? route('customer.learn', $course->slug) : $course->url()); ?>"><?php echo e($course->title); ?></a></h3>
                                <p><?php echo e($course->instructor->name ?? ''); ?></p>
                                <div class="course-meta"><span><i class="bi bi-play-circle"></i> <?php echo e($course->lessonsCount()); ?> lessons</span><?php if($course->duration): ?><span><i class="bi bi-clock"></i> <?php echo e($course->duration); ?></span><?php endif; ?></div>
                                <?php if($row['enrolled']): ?>
                                    <a href="<?php echo e(route('customer.learn', $course->slug)); ?>" class="btn btn-primary w-100 mt-3"><i class="bi bi-play-fill"></i> Start learning</a>
                                <?php else: ?>
                                    <p class="my-course__note">You'll get access as soon as we verify your payment for order #<?php echo e($row['order']->invoice); ?>.</p>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <div class="row g-4 mt-4">
            <div class="col-lg-8">
                <div class="panel h-100">
                    <h2 class="panel__title mb-3">Order history</h2>
                    <?php if($orders->isEmpty()): ?>
                        <p class="text-muted mb-0">You haven't placed any orders yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle account-orders mb-0">
                                <thead><tr><th>Order</th><th>Courses</th><th>Total</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><strong>#<?php echo e($order->invoice); ?></strong><small class="d-block text-muted"><?php echo e($order->created_at->format('d M Y')); ?></small></td>
                                            <td><?php echo e($order->items->pluck('title')->implode(', ')); ?></td>
                                            <td class="text-nowrap"><?php echo e(Storefront::money($order->total)); ?><small class="d-block text-muted"><?php echo e($order->methodName()); ?></small></td>
                                            <td><span class="order-status order-status--<?php echo e($order->status); ?>"><?php echo e(CourseOrder::STATUSES[$order->status] ?? ucfirst($order->status)); ?></span></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel h-100">
                    <h2 class="panel__title mb-3">Change password</h2>
                    <form method="post" action="<?php echo e(route('customer.password')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label" for="pwCurrent">Current password</label>
                            <input type="password" class="form-control <?php $__errorArgs = ['current_password', 'password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="pwCurrent" name="current_password" required autocomplete="current-password">
                            <?php $__errorArgs = ['current_password', 'password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pwNew">New password</label>
                            <input type="password" class="form-control <?php $__errorArgs = ['password', 'password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="pwNew" name="password" required minlength="6" autocomplete="new-password">
                            <?php $__errorArgs = ['password', 'password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pwConfirm">Confirm new password</label>
                            <input type="password" class="form-control" id="pwConfirm" name="password_confirmation" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-soft w-100">Update password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/account/dashboard.blade.php ENDPATH**/ ?>