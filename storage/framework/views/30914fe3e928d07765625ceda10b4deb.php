

<?php $__env->startSection('title', 'Checkout | TrainersBD'); ?>
<?php $__env->startSection('body_class', 'page-checkout'); ?>

<?php $__env->startSection('content'); ?>

<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('index')); ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('cart')); ?>">Cart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <h1 class="page-head__title">Checkout</h1>
            <?php echo $__env->make('frontend.partials.checkout-steps', ['step' => 2], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        <form class="row g-4" data-checkout-form novalidate hidden>
            <div class="col-lg-8">

                
                <div class="panel checkout-block">
                    <div class="checkout-block__head">
                        <span class="checkout-block__num">1</span>
                        <div>
                            <h2 class="panel__title">Your information</h2>
                            <?php if($customer): ?>
                                <p>Logged in as <strong><?php echo e($customer->name); ?></strong>. This course will be added to <a href="<?php echo e(route('customer.dashboard')); ?>">My Courses</a>.</p>
                            <?php else: ?>
                                <p>We'll create your student account with these details so you can open your courses anytime. Already have an account? <a href="<?php echo e(route('customer.login')); ?>?redirect=checkout">Log in</a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="ckName">Full name <span class="req">*</span></label>
                            <div class="input-icon"><i class="bi bi-person"></i>
                                <input type="text" class="form-control" id="ckName" name="name" value="<?php echo e($customer->name ?? ''); ?>" placeholder="e.g. Mahmudul Islam" required maxlength="100" autocomplete="name">
                            </div>
                            <div class="invalid-feedback">Please enter your full name.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ckMobile">Mobile number <span class="req">*</span></label>
                            <div class="input-icon"><i class="bi bi-phone"></i>
                                <input type="tel" class="form-control" id="ckMobile" name="mobile" value="<?php echo e($customer->mobile ?? ''); ?>" placeholder="01XXXXXXXXX" required pattern="^(\+?88)?01[3-9][0-9]{8}$" autocomplete="tel">
                            </div>
                            <div class="invalid-feedback">Enter a valid Bangladeshi mobile number (01XXXXXXXXX).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ckEmail">Email address <span class="req">*</span></label>
                            <div class="input-icon"><i class="bi bi-envelope"></i>
                                <input type="email" class="form-control" id="ckEmail" name="email" value="<?php echo e($customer->email ?? ''); ?>" placeholder="you@example.com" required autocomplete="email">
                            </div>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <?php if (! ($customer)): ?>
                            <div class="col-md-6">
                                <label class="form-label" for="ckPassword">Create a password <span class="req">*</span></label>
                                <div class="input-icon"><i class="bi bi-key"></i>
                                    <input type="password" class="form-control" id="ckPassword" name="password" placeholder="At least 6 characters" required minlength="6" maxlength="100" autocomplete="new-password">
                                </div>
                                <div class="invalid-feedback">Password must be at least 6 characters.</div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <p class="form-text mb-2"><i class="bi bi-shield-check"></i> Log in later with your mobile number and this password to see your courses.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="panel checkout-block">
                    <div class="checkout-block__head">
                        <span class="checkout-block__num">2</span>
                        <div>
                            <h2 class="panel__title">Choose payment method</h2>
                            <p>Send the total amount manually, then enter your payment details below.</p>
                        </div>
                    </div>

                    <div class="pay-options" role="radiogroup" aria-label="Payment method">
                        <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pay-option" style="--pm: <?php echo e($pm['color']); ?>">
                                <input type="radio" name="payment_method" value="<?php echo e($pm['key']); ?>" <?php echo e($i === 0 ? 'checked' : ''); ?> data-pay-radio>
                                <span class="pay-option__mark">
                                    <?php if($pm['key'] === 'bank'): ?><i class="bi bi-bank"></i><?php else: ?><?php echo e(mb_substr($pm['name'], 0, 1)); ?><?php endif; ?>
                                </span>
                                <span class="pay-option__text"><strong><?php echo e($pm['name']); ?></strong><small><?php echo e($pm['key'] === 'bank' ? 'Bank deposit / transfer' : 'Send Money'); ?></small></span>
                                <i class="bi bi-check-circle-fill pay-option__check"></i>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="pay-instructions" data-pay-panel="<?php echo e($pm['key']); ?>" style="--pm: <?php echo e($pm['color']); ?>" <?php if($i !== 0): ?> hidden <?php endif; ?>>
                            <div class="pay-instructions__top">
                                <div>
                                    <span class="pay-instructions__label"><?php echo e($pm['type']); ?></span>
                                    <div class="pay-instructions__account">
                                        <span><?php echo e($pm['account']); ?></span>
                                        <button type="button" class="copy-btn" data-copy="<?php echo e($pm['account']); ?>"><i class="bi bi-copy"></i> Copy</button>
                                    </div>
                                    <?php if(!empty($pm['details'])): ?><small class="d-block mt-1"><?php echo e($pm['details']); ?></small><?php endif; ?>
                                </div>
                                <div class="pay-instructions__amount">
                                    <span>Amount to send</span>
                                    <strong data-sum-total>৳0</strong>
                                </div>
                            </div>
                            <ol class="pay-steps">
                                <?php $__currentLoopData = $pm['step_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($s); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php ($payReq = config('trainersbd.payment_fields_required')); ?>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label" for="ckSender"><span data-sender-label>Sender number</span> <?php if($payReq): ?><span class="req">*</span><?php else: ?><span class="text-muted fw-normal">(optional)</span><?php endif; ?></label>
                            <div class="input-icon"><i class="bi bi-phone-vibrate"></i>
                                <input type="text" class="form-control" id="ckSender" name="sender_number" placeholder="Number you paid from" <?php if($payReq): ?> required <?php endif; ?> maxlength="30">
                            </div>
                            <div class="invalid-feedback">Please enter the number / account you paid from.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ckTrx">Transaction ID <?php if($payReq): ?><span class="req">*</span><?php else: ?><span class="text-muted fw-normal">(optional)</span><?php endif; ?></label>
                            <div class="input-icon"><i class="bi bi-receipt"></i>
                                <input type="text" class="form-control text-uppercase" id="ckTrx" name="transaction_id" placeholder="e.g. 8N7A6D5E4F" <?php if($payReq): ?> required minlength="6" <?php endif; ?> maxlength="40">
                            </div>
                            <div class="invalid-feedback">Please enter the transaction ID from your payment.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="ckNote">Note <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea class="form-control" id="ckNote" name="note" rows="2" placeholder="Anything we should know about your order?" maxlength="500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                <div class="summary-card sticky-lg-top">
                    <h2 class="panel__title">Your order</h2>
                    <ul class="mini-cart" data-checkout-items></ul>
                    <dl class="summary-lines">
                        <div><dt>Original price</dt><dd data-sum-original>৳0</dd></div>
                        <div class="summary-lines__discount"><dt>Discount</dt><dd data-sum-discount>-৳0</dd></div>
                        <div class="summary-lines__total"><dt>Total</dt><dd data-sum-total>৳0</dd></div>
                    </dl>

                    <div class="form-check terms-check">
                        <input class="form-check-input" type="checkbox" id="ckTerms" name="terms" required>
                        <label class="form-check-label" for="ckTerms">I confirm the payment details are correct and agree to the <a href="#">Terms</a> &amp; <a href="#">Refund Policy</a>.</label>
                        <div class="invalid-feedback">Please confirm to continue.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" data-place-order>
                        <i class="bi bi-lock-fill"></i> Place Order
                    </button>

                    <div class="summary-info">
                        <i class="bi bi-info-circle"></i>
                        <p>After you place the order, our team will verify your payment and contact you within a few hours with course access.</p>
                    </div>
                </div>
            </div>
        </form>

        <div class="empty-state" data-checkout-empty hidden>
            <span class="empty-state__icon"><i class="bi bi-bag-x"></i></span>
            <h2>Nothing to check out</h2>
            <p>Your cart is empty. Add a course to your cart to continue.</p>
            <a href="<?php echo e(route('index')); ?>#courses" class="btn btn-primary btn-lg">Browse Courses</a>
        </div>

        <div class="cart-loading sk-wrap" data-cart-loading aria-busy="true" aria-label="Loading checkout">
            <div class="row g-4">
                <div class="col-lg-7"><div class="sk-card"><span class="sk sk-line w-50"></span><div class="sk-grid"><span class="sk sk-input"></span><span class="sk sk-input"></span><span class="sk sk-input"></span><span class="sk sk-input"></span></div><span class="sk sk-line w-25"></span><div class="sk-grid sk-grid--3"><span class="sk sk-tile"></span><span class="sk sk-tile"></span><span class="sk sk-tile"></span></div></div></div>
                <div class="col-lg-5"><div class="sk-card"><div class="sk-row"><span class="sk sk-thumb"></span><div class="sk-lines"><span class="sk sk-line w-75"></span><span class="sk sk-line w-50"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-row"><span class="sk sk-thumb"></span><div class="sk-lines"><span class="sk sk-line w-75"></span><span class="sk sk-line w-50"></span><span class="sk sk-line w-25"></span></div></div><span class="sk sk-line"></span><span class="sk sk-btn"></span></div></div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/checkout.blade.php ENDPATH**/ ?>