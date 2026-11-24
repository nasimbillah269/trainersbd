@extends('frontend.layouts.app')

@section('title', 'Checkout | TrainersBD')
@section('body_class', 'page-checkout')

@section('content')

<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart') }}">Cart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <h1 class="page-head__title">Checkout</h1>
            @include('frontend.partials.checkout-steps', ['step' => 2])
        </div>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        <form class="row g-4" data-checkout-form novalidate hidden>
            <div class="col-lg-8">

                {{-- Step 1: student info --}}
                <div class="panel checkout-block">
                    <div class="checkout-block__head">
                        <span class="checkout-block__num">1</span>
                        <div>
                            <h2 class="panel__title">Your information</h2>
                            <p>No account needed. We'll use these details to contact you about your enrollment.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="ckName">Full name <span class="req">*</span></label>
                            <div class="input-icon"><i class="bi bi-person"></i>
                                <input type="text" class="form-control" id="ckName" name="name" placeholder="e.g. Mahmudul Islam" required maxlength="100" autocomplete="name">
                            </div>
                            <div class="invalid-feedback">Please enter your full name.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ckMobile">Mobile number <span class="req">*</span></label>
                            <div class="input-icon"><i class="bi bi-phone"></i>
                                <input type="tel" class="form-control" id="ckMobile" name="mobile" placeholder="01XXXXXXXXX" required pattern="^(\+?88)?01[3-9][0-9]{8}$" autocomplete="tel">
                            </div>
                            <div class="invalid-feedback">Enter a valid Bangladeshi mobile number (01XXXXXXXXX).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ckEmail">Email address <span class="req">*</span></label>
                            <div class="input-icon"><i class="bi bi-envelope"></i>
                                <input type="email" class="form-control" id="ckEmail" name="email" placeholder="you@example.com" required autocomplete="email">
                            </div>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                    </div>
                </div>

                {{-- Step 2: payment method --}}
                <div class="panel checkout-block">
                    <div class="checkout-block__head">
                        <span class="checkout-block__num">2</span>
                        <div>
                            <h2 class="panel__title">Choose payment method</h2>
                            <p>Send the total amount manually, then enter your payment details below.</p>
                        </div>
                    </div>

                    <div class="pay-options" role="radiogroup" aria-label="Payment method">
                        @foreach($paymentMethods as $i => $pm)
                            <label class="pay-option" style="--pm: {{ $pm['color'] }}">
                                <input type="radio" name="payment_method" value="{{ $pm['key'] }}" {{ $i === 0 ? 'checked' : '' }} data-pay-radio>
                                <span class="pay-option__mark">
                                    @if($pm['key'] === 'bank')<i class="bi bi-bank"></i>@else{{ mb_substr($pm['name'], 0, 1) }}@endif
                                </span>
                                <span class="pay-option__text"><strong>{{ $pm['name'] }}</strong><small>{{ $pm['key'] === 'bank' ? 'Bank deposit / transfer' : 'Send Money' }}</small></span>
                                <i class="bi bi-check-circle-fill pay-option__check"></i>
                            </label>
                        @endforeach
                    </div>

                    @foreach($paymentMethods as $i => $pm)
                        <div class="pay-instructions" data-pay-panel="{{ $pm['key'] }}" style="--pm: {{ $pm['color'] }}" @if($i !== 0) hidden @endif>
                            <div class="pay-instructions__top">
                                <div>
                                    <span class="pay-instructions__label">{{ $pm['type'] }}</span>
                                    <div class="pay-instructions__account">
                                        <span>{{ $pm['account'] }}</span>
                                        <button type="button" class="copy-btn" data-copy="{{ $pm['account'] }}"><i class="bi bi-copy"></i> Copy</button>
                                    </div>
                                    @if(!empty($pm['details']))<small class="d-block mt-1">{{ $pm['details'] }}</small>@endif
                                </div>
                                <div class="pay-instructions__amount">
                                    <span>Amount to send</span>
                                    <strong data-sum-total>৳0</strong>
                                </div>
                            </div>
                            <ol class="pay-steps">
                                @foreach($pm['step_list'] as $s)
                                    <li>{{ $s }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endforeach

                    @php($payReq = config('trainersbd.payment_fields_required'))
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label" for="ckSender"><span data-sender-label>Sender number</span> @if($payReq)<span class="req">*</span>@else<span class="text-muted fw-normal">(optional)</span>@endif</label>
                            <div class="input-icon"><i class="bi bi-phone-vibrate"></i>
                                <input type="text" class="form-control" id="ckSender" name="sender_number" placeholder="Number you paid from" @if($payReq) required @endif maxlength="30">
                            </div>
                            <div class="invalid-feedback">Please enter the number / account you paid from.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ckTrx">Transaction ID @if($payReq)<span class="req">*</span>@else<span class="text-muted fw-normal">(optional)</span>@endif</label>
                            <div class="input-icon"><i class="bi bi-receipt"></i>
                                <input type="text" class="form-control text-uppercase" id="ckTrx" name="transaction_id" placeholder="e.g. 8N7A6D5E4F" @if($payReq) required minlength="6" @endif maxlength="40">
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

            {{-- Summary --}}
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
            <a href="{{ route('index') }}#courses" class="btn btn-primary btn-lg">Browse Courses</a>
        </div>

        <div class="cart-loading sk-wrap" data-cart-loading aria-busy="true" aria-label="Loading checkout">
            <div class="row g-4">
                <div class="col-lg-7"><div class="sk-card"><span class="sk sk-line w-50"></span><div class="sk-grid"><span class="sk sk-input"></span><span class="sk sk-input"></span><span class="sk sk-input"></span><span class="sk sk-input"></span></div><span class="sk sk-line w-25"></span><div class="sk-grid sk-grid--3"><span class="sk sk-tile"></span><span class="sk sk-tile"></span><span class="sk sk-tile"></span></div></div></div>
                <div class="col-lg-5"><div class="sk-card"><div class="sk-row"><span class="sk sk-thumb"></span><div class="sk-lines"><span class="sk sk-line w-75"></span><span class="sk sk-line w-50"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-row"><span class="sk sk-thumb"></span><div class="sk-lines"><span class="sk sk-line w-75"></span><span class="sk sk-line w-50"></span><span class="sk sk-line w-25"></span></div></div><span class="sk sk-line"></span><span class="sk sk-btn"></span></div></div>
            </div>
        </div>
    </div>
</section>

@endsection
