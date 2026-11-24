@extends('frontend.layouts.app')

@section('title', 'Your Cart | TrainersBD')
@section('body_class', 'page-cart')

@section('content')

<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <h1 class="page-head__title">Shopping Cart <span class="page-head__count" data-cart-heading-count></span></h1>
            @include('frontend.partials.checkout-steps', ['step' => 1])
        </div>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        {{-- Filled state --}}
        <div class="row g-4" data-cart-filled hidden>
            <div class="col-lg-8">
                <div class="panel">
                    <div class="panel__head">
                        <h2 class="panel__title">Courses in your cart</h2>
                        <button type="button" class="btn btn-link text-danger px-0" data-cart-clear><i class="bi bi-trash3"></i> Clear cart</button>
                    </div>
                    <ul class="cart-list" data-cart-list></ul>
                </div>

                <div class="assurance">
                    <div><i class="bi bi-shield-check"></i><span><strong>Verified payments</strong>Every payment is checked by our team</span></div>
                    <div><i class="bi bi-person-check"></i><span><strong>No registration</strong>Just your name, mobile and email</span></div>
                    <div><i class="bi bi-headset"></i><span><strong>Personal support</strong>We help you every step of the way</span></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card sticky-lg-top">
                    <h2 class="panel__title">Order summary</h2>
                    <dl class="summary-lines">
                        <div><dt>Original price</dt><dd data-sum-original>৳0</dd></div>
                        <div class="summary-lines__discount"><dt>Discount</dt><dd data-sum-discount>-৳0</dd></div>
                        <div class="summary-lines__total"><dt>Total</dt><dd data-sum-total>৳0</dd></div>
                    </dl>
                    <p class="summary-save" data-sum-save hidden><i class="bi bi-tag-fill"></i> You are saving <strong></strong> on this order</p>
                    <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100">Proceed to Checkout <i class="bi bi-arrow-right"></i></a>
                    <a href="{{ route('index') }}#courses" class="btn btn-link w-100 mt-1">Continue browsing</a>
                    <div class="summary-pay">
                        <span>Pay securely with</span>
                        <div class="pay-chips">
                            @foreach(\App\Support\Storefront::paymentMethods() as $pm)
                                <span class="pay-chip pay-chip--sm" style="--chip: {{ $pm['color'] }}">{{ $pm['key'] === 'bank' ? 'Bank' : $pm['name'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Empty state --}}
        <div class="empty-state" data-cart-empty hidden>
            <span class="empty-state__icon"><i class="bi bi-bag"></i></span>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any course yet. Explore our courses and start learning today.</p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('index') }}#courses" class="btn btn-primary btn-lg">Browse Courses</a>
                <a href="{{ route('index') }}#free-courses" class="btn btn-outline-dark btn-lg">Watch Free Courses</a>
            </div>
        </div>

        {{-- Loading placeholder (until JS reads the cart) --}}
        <div class="cart-loading sk-wrap" data-cart-loading aria-busy="true" aria-label="Loading cart">
            <div class="row g-4">
                <div class="col-lg-8"><div class="sk-card"><div class="sk-row"><span class="sk sk-thumb"></span><div class="sk-lines"><span class="sk sk-line w-75"></span><span class="sk sk-line w-50"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-row"><span class="sk sk-thumb"></span><div class="sk-lines"><span class="sk sk-line w-75"></span><span class="sk sk-line w-50"></span><span class="sk sk-line w-25"></span></div></div><div class="sk-row"><span class="sk sk-thumb"></span><div class="sk-lines"><span class="sk sk-line w-75"></span><span class="sk sk-line w-50"></span><span class="sk sk-line w-25"></span></div></div></div></div>
                <div class="col-lg-4"><div class="sk-card"><span class="sk sk-line w-50"></span><span class="sk sk-line"></span><span class="sk sk-line"></span><span class="sk sk-btn"></span></div></div>
            </div>
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Recommended</span>
                <h2>You might also like</h2>
            </div>
        </div>
        <div class="course-grid">
            @foreach($suggested as $course)
                @include('frontend.partials.course-card', ['course' => $course])
            @endforeach
        </div>
    </div>
</section>

@endsection
