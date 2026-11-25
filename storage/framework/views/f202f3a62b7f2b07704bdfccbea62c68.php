<?php $__env->startSection('title'); ?><title>Order #<?php echo e($order->invoice); ?> | <?php echo e(general()->title); ?> Admin</title><?php $__env->stopSection(); ?>
<?php use App\Support\Storefront; use App\Models\CourseOrder; ?>

<?php $__env->startSection('contents'); ?>
<div class="adm-page-head">
    <div>
        <ul class="adm-crumbs"><li><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li><a href="<?php echo e(route('admin.course-orders.index')); ?>">Orders</a></li><li>#<?php echo e($order->invoice); ?></li></ul>
        <h1>Order <span class="adm-mono">#<?php echo e($order->invoice); ?></span></h1>
        <p>
            <span class="adm-badge adm-badge--<?php echo e($order->status); ?>"><?php echo e(CourseOrder::STATUSES[$order->status]); ?></span>
            <span class="adm-badge adm-badge--<?php echo e($order->payment_status); ?>">Payment <?php echo e(strtolower(CourseOrder::PAYMENT_STATUSES[$order->payment_status])); ?></span>
            &nbsp;Placed <?php echo e($order->created_at->format('d M Y, h:i A')); ?> (<?php echo e($order->created_at->diffForHumans()); ?>)
        </p>
    </div>
    <div class="adm-page-head__actions">
        <?php if($order->status !== 'confirmed'): ?>
            <form method="post" action="<?php echo e(route('admin.course-orders.quick', $order)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="confirm"><button class="adm-btn adm-btn--success" data-confirm="Mark payment <?php echo e($order->transaction_id); ?> as verified and confirm this order?"><i class="fa-solid fa-circle-check"></i> Verify &amp; confirm</button></form>
        <?php endif; ?>
        <?php if(!in_array($order->status, ['hold', 'cancelled'])): ?>
            <form method="post" action="<?php echo e(route('admin.course-orders.quick', $order)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="hold"><button class="adm-btn adm-btn--warning"><i class="fa-solid fa-pause"></i> Hold</button></form>
        <?php endif; ?>
        <?php if($order->status !== 'cancelled'): ?>
            <form method="post" action="<?php echo e(route('admin.course-orders.quick', $order)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="cancel"><button class="adm-btn adm-btn--danger" data-confirm="Cancel order #<?php echo e($order->invoice); ?>?"><i class="fa-solid fa-xmark"></i> Cancel</button></form>
        <?php endif; ?>
    </div>
</div>

<?php echo $__env->make('admin.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-xl-8">
        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-wallet"></i> Payment to verify</h3></div>
            <div class="adm-card__body">
                <div class="adm-paybox adm-paybox--<?php echo e($order->payment_method); ?>">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <small class="text-muted d-block">Transaction ID</small>
                            <div class="adm-paybox__trx adm-mono"><?php echo e($order->transaction_id); ?> <button type="button" class="adm-copy" data-copy="<?php echo e($order->transaction_id); ?>" title="Copy"><i class="fa-regular fa-copy"></i></button></div>
                        </div>
                        <div class="col-md-3 col-6">
                            <small class="text-muted d-block">Method</small>
                            <strong class="text-dark"><?php echo e($order->methodName()); ?></strong>
                        </div>
                        <div class="col-md-4 col-6">
                            <small class="text-muted d-block">Sent from</small>
                            <strong class="text-dark adm-mono"><?php echo e($order->sender_number); ?></strong>
                        </div>
                    </div>
                    <hr style="border-color:rgba(0,0,0,.06)">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem">
                        <span class="text-muted">Amount the student should have sent</span>
                        <strong style="font-size:1.4rem;color:var(--a-ink)"><?php echo e(Storefront::money($order->total)); ?></strong>
                    </div>
                </div>
                <p class="adm-hint mt-3 mb-0"><i class="fa-regular fa-lightbulb"></i> Check your <?php echo e($order->methodName()); ?> statement for this transaction ID and amount before confirming.</p>

                <?php if($duplicates->count()): ?>
                    <div class="adm-alert adm-alert--warning mt-3 mb-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div><strong>Other orders with the same mobile number or transaction ID:</strong>
                            <ul><?php $__currentLoopData = $duplicates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><a href="<?php echo e(route('admin.course-orders.show', $d)); ?>">#<?php echo e($d->invoice); ?></a> — <?php echo e(CourseOrder::STATUSES[$d->status]); ?>, TrxID <?php echo e($d->transaction_id); ?>, <?php echo e($d->created_at->format('d M Y')); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-layer-group"></i> Courses ordered</h3></div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th>Course</th><th class="num">Regular</th><th class="num">Price</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><div class="adm-cell">
                                    <?php if($item->course && $item->course->thumbnailUrl()): ?><img src="<?php echo e($item->course->thumbnailUrl()); ?>" class="adm-thumb" alt=""><?php else: ?><span class="adm-thumb"><i class="fa-regular fa-image"></i></span><?php endif; ?>
                                    <div><strong><?php echo e($item->title); ?></strong>
                                        <?php if($item->course): ?><small><a href="<?php echo e(route('admin.courses.edit', $item->course_id)); ?>">Edit course</a> &middot; <a href="<?php echo e($item->course->url()); ?>" target="_blank">View</a></small><?php else: ?><small>Course deleted</small><?php endif; ?>
                                    </div>
                                </div></td>
                                <td class="num text-muted"><?php if($item->old_price > $item->price): ?><s><?php echo e(Storefront::money($item->old_price)); ?></s><?php else: ?> — <?php endif; ?></td>
                                <td class="num"><b><?php echo e(Storefront::money($item->price)); ?></b></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="adm-card__foot">
                <dl class="adm-dl ml-auto" style="max-width:320px">
                    <div><dt>Subtotal</dt><dd><?php echo e(Storefront::money($order->subtotal)); ?></dd></div>
                    <div><dt>Discount</dt><dd style="color:var(--a-success)">-<?php echo e(Storefront::money($order->discount)); ?></dd></div>
                    <div class="adm-total"><dt>Total</dt><dd><?php echo e(Storefront::money($order->total)); ?></dd></div>
                </dl>
            </div>
        </div>

        <?php if($order->note): ?>
            <div class="adm-card">
                <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-regular fa-message"></i> Note from student</h3></div>
                <div class="adm-card__body"><?php echo e($order->note); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-xl-4">
        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-regular fa-user"></i> Student</h3></div>
            <div class="adm-card__body">
                <div class="adm-cell mb-3"><span class="adm-avatar adm-avatar--lg"><?php echo e(\App\Support\Storefront::initials($order->name)); ?></span><div><strong style="font-size:1.05rem"><?php echo e($order->name); ?></strong><small><?php echo e($order->email); ?></small></div></div>
                <div class="d-flex flex-wrap" style="gap:.5rem">
                    <a href="tel:<?php echo e($order->mobile); ?>" class="adm-btn adm-btn--sm"><i class="fa-solid fa-phone"></i> <?php echo e($order->mobile); ?></a>
                    <a href="https://wa.me/<?php echo e(preg_replace('/^0/', '880', preg_replace('/\D/', '', $order->mobile))); ?>" target="_blank" rel="noopener" class="adm-btn adm-btn--sm"><i class="fa-brands fa-whatsapp" style="color:#25D366"></i> WhatsApp</a>
                    <a href="mailto:<?php echo e($order->email); ?>?subject=<?php echo e(rawurlencode('Your TrainersBD order #' . $order->invoice)); ?>" class="adm-btn adm-btn--sm"><i class="fa-regular fa-envelope"></i> Email</a>
                </div>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-pen-to-square"></i> Update order</h3></div>
            <form method="post" action="<?php echo e(route('admin.course-orders.update', $order)); ?>" class="adm-card__body">
                <?php echo csrf_field(); ?>
                <div class="adm-field">
                    <label class="adm-label" for="oStatus">Order status</label>
                    <select class="adm-select" id="oStatus" name="status">
                        <?php $__currentLoopData = CourseOrder::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if($order->status === $k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="oPay">Payment status</label>
                    <select class="adm-select" id="oPay" name="payment_status">
                        <?php $__currentLoopData = CourseOrder::PAYMENT_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if($order->payment_status === $k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="adm-field">
                    <label class="adm-label" for="oNote">Internal note</label>
                    <textarea class="adm-textarea" id="oNote" name="admin_note" rows="3" placeholder="Only visible to admins, e.g. enrolled in WhatsApp group on 24 Nov"><?php echo e(old('admin_note', $order->admin_note)); ?></textarea>
                </div>
                <button class="adm-btn adm-btn--primary adm-btn--block"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </form>
        </div>

        <div class="adm-card">
            <div class="adm-card__head"><h3 class="adm-card__title"><i class="fa-solid fa-timeline"></i> Activity</h3></div>
            <div class="adm-card__body">
                <ul class="adm-timeline">
                    <li><b>Order placed</b><small><?php echo e($order->created_at->format('d M Y, h:i A')); ?> &middot; IP <?php echo e($order->ip ?: '—'); ?></small></li>
                    <?php if($order->verified_at): ?><li><b>Payment verified</b><small><?php echo e($order->verified_at->format('d M Y, h:i A')); ?> by <?php echo e($order->verifier->name ?? 'admin'); ?></small></li><?php endif; ?>
                    <?php if($order->updated_at->gt($order->created_at)): ?><li><b>Last updated</b><small><?php echo e($order->updated_at->format('d M Y, h:i A')); ?></small></li><?php endif; ?>
                </ul>
                <form method="post" action="<?php echo e(route('admin.course-orders.destroy', $order)); ?>" class="mt-2"><?php echo csrf_field(); ?><button class="adm-btn adm-btn--sm adm-btn--danger" data-confirm="Delete this order permanently?"><i class="fa-regular fa-trash-can"></i> Delete order</button></form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script>
    $(document).on('click', '[data-copy]', function () {
        var b = this, t = b.getAttribute('data-copy');
        (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject()).catch(function () {}).then(function () {
            b.innerHTML = '<i class="fa-solid fa-check"></i>'; setTimeout(function () { b.innerHTML = '<i class="fa-regular fa-copy"></i>'; }, 1500);
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/course-orders/show.blade.php ENDPATH**/ ?>