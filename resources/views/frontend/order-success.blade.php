@extends('frontend.layouts.app')

@php
    use App\Support\Storefront;
    use App\Models\CourseOrder;
    $site = Storefront::site();
    $wa = preg_replace('/^0/', '880', preg_replace('/\D/', '', $site['phone']));
    $confirmed = $order->status === 'confirmed';
    $cancelled = $order->status === 'cancelled';
    $waText = rawurlencode('Hello TrainersBD, I placed order #' . $order->invoice . ' (' . Storefront::money($order->total) . ' via ' . $order->methodName() . '). Please verify my payment.');
@endphp

@section('title', 'Order #' . $order->invoice . ' | ' . $site['name'])
@section('body_class', 'page-success')
@push('css')<meta name="robots" content="noindex, nofollow">@endpush

@section('content')
<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order confirmation</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <h1 class="page-head__title">Order confirmation</h1>
            @include('frontend.partials.checkout-steps', ['step' => 3])
        </div>
    </div>
</section>

<section class="section pt-4">
    <div class="container">
        {{-- Hero --}}
        <div class="success-hero {{ $confirmed ? 'is-confirmed' : '' }} {{ $cancelled ? 'is-cancelled' : '' }}">
            <div class="success-hero__icon"><i class="bi {{ $cancelled ? 'bi-x-lg' : 'bi-check-lg' }}"></i></div>
            <div class="success-hero__text">
                @if($cancelled)
                    <h2>This order was cancelled</h2>
                    <p>Please contact us if you think this is a mistake.</p>
                @elseif($confirmed)
                    <h2>You're enrolled, {{ \Illuminate\Support\Str::before($order->name, ' ') ?: $order->name }}! 🎉</h2>
                    <p>Your payment has been verified. Our team will share your course access on <strong>{{ $order->mobile }}</strong>.</p>
                @else
                    <h2>Thank you, {{ \Illuminate\Support\Str::before($order->name, ' ') ?: $order->name }}! Your order is placed.</h2>
                    <p>We've received your order. Our team will verify your payment and contact you on <strong>{{ $order->mobile }}</strong> with course access, usually within a few hours.</p>
                @endif
            </div>
            <div class="success-hero__id">
                <small>Order number</small>
                <strong>#{{ $order->invoice }}</strong>
                <button type="button" class="copy-btn" data-copy="{{ $order->invoice }}"><i class="bi bi-copy"></i> Copy</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Progress --}}
                <div class="panel mb-4">
                    <h2 class="panel__title mb-4">Order status</h2>
                    <ol class="track">
                        <li class="is-done">
                            <span class="track__dot"><i class="bi bi-check-lg"></i></span>
                            <div><strong>Order placed</strong><small>{{ $order->created_at->format('d M Y, h:i A') }}</small></div>
                        </li>
                        <li class="{{ $order->payment_status === 'verified' ? 'is-done' : ($cancelled ? '' : 'is-current') }}">
                            <span class="track__dot">@if($order->payment_status === 'verified')<i class="bi bi-check-lg"></i>@else<i class="bi bi-hourglass-split"></i>@endif</span>
                            <div><strong>Payment verification</strong><small>{{ $order->payment_status === 'verified' ? 'Verified' . ($order->verified_at ? ' on ' . $order->verified_at->format('d M Y') : '') : 'Our team is checking your ' . $order->methodName() . ' payment' }}</small></div>
                        </li>
                        <li class="{{ $confirmed ? 'is-done' : '' }}">
                            <span class="track__dot">@if($confirmed)<i class="bi bi-check-lg"></i>@else<i class="bi bi-mortarboard"></i>@endif</span>
                            <div><strong>Course access</strong><small>{{ $confirmed ? 'Enrolled' : 'Shared by our team after verification' }}</small></div>
                        </li>
                    </ol>
                    <div class="status-row">
                        <span>Order: <b class="status-badge status-badge--{{ $order->status }}">{{ CourseOrder::STATUSES[$order->status] }}</b></span>
                        <span>Payment: <b class="status-badge status-badge--{{ $order->payment_status }}">{{ CourseOrder::PAYMENT_STATUSES[$order->payment_status] }}</b></span>
                    </div>
                </div>

                {{-- Courses --}}
                <div class="panel mb-4">
                    <div class="panel__head mb-2">
                        <h2 class="panel__title">Courses in this order</h2>
                        <span class="text-muted small">{{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('course', $order->items->count()) }}</span>
                    </div>
                    <ul class="cart-list">
                        @foreach($order->items as $item)
                            @php($c = $item->course)
                            <li class="cart-item">
                                <a href="{{ $c ? $c->url() : '#' }}" class="course-thumb cart-item__thumb {{ $c && $c->thumbnailUrl() ? 'course-thumb--img' : '' }}">
                                    @if($c && $c->thumbnailUrl())<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="{{ $c->thumbnailUrl() }}" alt="" class="lazy" decoding="async">@else<i class="bi {{ $c ? $c->icon() : 'bi-journal' }} course-thumb__icon"></i>@endif
                                </a>
                                <div class="cart-item__info">
                                    <h3>@if($c)<a href="{{ $c->url() }}">{{ $item->title }}</a>@else{{ $item->title }}@endif</h3>
                                    @if($c && $c->category)<p>{{ $c->category->name }}</p>@endif
                                </div>
                                <div class="cart-item__side">
                                    <div class="price">
                                        <span class="price__now">{{ Storefront::money($item->price) }}</span>
                                        @if($item->old_price > $item->price)<span class="price__old">{{ Storefront::money($item->old_price) }}</span>@endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Next steps --}}
                <div class="panel">
                    <h2 class="panel__title mb-3">What happens next?</h2>
                    <div class="next-steps">
                        <div><span>1</span><p><strong>We verify your payment</strong>Our team matches your {{ $order->methodName() }} transaction with this order.</p></div>
                        <div><span>2</span><p><strong>We contact you</strong>You'll get a call or message on {{ $order->mobile }} and an email at {{ $order->email }}.</p></div>
                        <div><span>3</span><p><strong>Start learning</strong>You'll receive course access and join the private support group.</p></div>
                    </div>
                    @if(!$order->transaction_id)
                        <div class="success-note"><i class="bi bi-info-circle"></i><span>You didn't add a transaction ID. To verify faster, send us your payment details on WhatsApp with your order number.</span></div>
                    @endif
                </div>
            </div>

            {{-- Summary --}}
            <div class="col-lg-4">
                <div class="summary-card sticky-lg-top">
                    <h2 class="panel__title">Payment summary</h2>
                    <dl class="summary-lines">
                        <div><dt>Original price</dt><dd>{{ Storefront::money($order->subtotal) }}</dd></div>
                        @if($order->discount > 0)<div class="summary-lines__discount"><dt>Discount</dt><dd>-{{ Storefront::money($order->discount) }}</dd></div>@endif
                        <div class="summary-lines__total"><dt>Total</dt><dd>{{ Storefront::money($order->total) }}</dd></div>
                    </dl>

                    <div class="pay-receipt" style="--pm: {{ $method['color'] ?? '#003080' }}">
                        <div><span>Paid via</span><strong>{{ $order->methodName() }}</strong></div>
                        @if($method)<div><span>Sent to</span><strong class="mono">{{ $method['account'] }}</strong></div>@endif
                        <div><span>Sender</span><strong class="mono">{{ $order->sender_number ?: '—' }}</strong></div>
                        <div><span>Transaction ID</span><strong class="mono">{{ $order->transaction_id ?: '—' }}</strong></div>
                    </div>

                    <dl class="summary-lines mt-3 mb-0">
                        <div><dt>Name</dt><dd>{{ $order->name }}</dd></div>
                        <div><dt>Mobile</dt><dd>{{ $order->mobile }}</dd></div>
                        <div><dt>Email</dt><dd class="text-break">{{ $order->email }}</dd></div>
                    </dl>

                    <div class="d-grid gap-2 mt-4">
                        @if($site['phone'])
                            <a href="https://wa.me/{{ $wa }}?text={{ $waText }}" target="_blank" rel="noopener" class="btn btn-success btn-lg"><i class="bi bi-whatsapp"></i> Message us on WhatsApp</a>
                            <a href="tel:{{ Storefront::tel() }}" class="btn btn-outline-dark"><i class="bi bi-telephone"></i> Call {{ $site['phone'] }}</a>
                        @endif
                        <button type="button" class="btn btn-link" onclick="window.print()"><i class="bi bi-printer"></i> Print / save this page</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="success-actions">
            @php($mine = $order->user_id && auth()->id() === $order->user_id)
            @if($mine)
                <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-lg"><i class="bi bi-person-circle"></i> Go to My Courses</a>
            @endif
            <a href="{{ route('courses.index') }}" class="btn {{ $mine ? 'btn-outline-dark' : 'btn-primary' }} btn-lg">Browse more courses <i class="bi bi-arrow-right"></i></a>
            <a href="{{ route('courses.free') }}" class="btn btn-outline-dark btn-lg"><i class="bi bi-youtube"></i> Watch free courses</a>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
    // The order is saved: make sure the browser cart is empty
    if (window.TBDCart) { window.TBDCart.clear(); }
</script>
@endpush
