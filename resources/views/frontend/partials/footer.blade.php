@php
    $site = \App\Support\Storefront::site();
    $tel  = \App\Support\Storefront::tel();
    $socialIcons = ['facebook' => 'bi-facebook', 'youtube' => 'bi-youtube', 'linkedin' => 'bi-linkedin', 'instagram' => 'bi-instagram'];
@endphp
<footer class="site-footer" id="contact">
    <div class="container">
        <div class="footer-cta">
            <div>
                <h3>Not sure which course is right for you?</h3>
                <p>Call or message us and our team will help you choose, free of charge.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($site['phone'])<a href="tel:{{ $tel }}" class="btn btn-light btn-lg"><i class="bi bi-telephone"></i> Call Us</a>@endif
                @isset($site['social']['facebook'])<a href="{{ $site['social']['facebook'] }}" class="btn btn-outline-light btn-lg" target="_blank" rel="noopener"><i class="bi bi-messenger"></i> Message on Facebook</a>@endisset
            </div>
        </div>

        <div class="row g-5 footer-main">
            <div class="col-lg-4">
                <a class="brand brand--card" href="{{ route('index') }}">
                    <img src="{{ asset(general()->footer_logo ? assetPath(general()->footer_logo) : general()->logo()) }}" alt="{{ $site['name'] }}" class="brand__logo" width="175" height="82">
                </a>
                <p class="footer-about">{{ $site['meta_description'] ?: $site['tagline'] }}</p>
                <div class="footer-social">
                    @foreach($site['social'] as $net => $link)
                        <a href="{{ $link }}" aria-label="{{ ucfirst($net) }}" target="_blank" rel="noopener"><i class="bi {{ $socialIcons[$net] }}"></i></a>
                    @endforeach
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Explore</h6>
                <ul>
                    <li><a href="{{ route('courses.index') }}">All Courses</a></li>
                    <li><a href="{{ route('courses.free') }}">Free Courses</a></li>
                    <li><a href="{{ route('index') }}#how-it-works">How to Enroll</a></li>
                    <li><a href="{{ route('cart') }}">My Cart</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Categories</h6>
                <ul>
                    @foreach(\App\Support\Storefront::categories()->where('courses_count', '>', 0)->take(5) as $cat)
                        <li><a href="{{ route('courses.category', $cat->slug) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>Contact</h6>
                <ul class="footer-contact">
                    <li><i class="bi bi-geo-alt"></i> {{ $site['address'] }}</li>
                    @if($site['phone'])<li><i class="bi bi-telephone"></i> <a href="tel:{{ $tel }}">{{ $site['phone'] }}</a></li>@endif
                    @if($site['email'])<li><i class="bi bi-envelope"></i> <a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></li>@endif
                </ul>
                <h6 class="mt-4">We accept</h6>
                <div class="pay-chips">
                    @foreach(\App\Support\Storefront::paymentMethods() as $pm)
                        <span class="pay-chip" style="--chip: {{ $pm['key'] === 'bank' ? '#93C5FD' : $pm['color'] }}">@if($pm['key'] === 'bank')<i class="bi bi-bank"></i> @endif{{ $pm['name'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ $site['name'] }}. All rights reserved.</span>
            <span><a href="{{ url('privacy-policy') }}">Privacy Policy</a> &middot; <a href="{{ url('terms-and-conditions') }}">Terms</a> &middot; <a href="{{ url('refund-and-return-policy') }}">Refund Policy</a></span>
        </div>
    </div>
</footer>
