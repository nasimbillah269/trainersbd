@extends('frontend.layouts.app')

@php
    use App\Support\Storefront;
    $site = Storefront::site();
    $isContact = $page->template === 'Contact Us';
    $banner = $page->imageFile ? asset($page->image()) : null;
    $footerPages = \App\Models\Post::where('type', 0)->where('status', 'active')
        ->whereIn('slug', ['about-us', 'contact-us', 'privacy-policy', 'terms-and-conditions', 'refund-and-return-policy'])
        ->get(['id', 'name', 'slug']);
    $wa = preg_replace('/^0/', '880', preg_replace('/\D/', '', $site['phone']));
@endphp

@section('title', $page->seo_title ?: $page->name . ' | ' . $site['name'])
@section('meta_description', $page->seo_description ?: (\Illuminate\Support\Str::limit(strip_tags((string) ($page->short_description ?: $page->description)), 160) ?: $site['meta_description']))
@if($page->seo_keyword)
    @section('meta_keywords', $page->seo_keyword)
@endif

@section('content')
<section class="page-hero">
    <div class="hero__glow hero__glow--1"></div>
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb--light">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $page->name }}</li>
            </ol>
        </nav>
        <h1>{{ $page->name }}</h1>
        @if($page->short_description)
            <p>{{ strip_tags($page->short_description) }}</p>
        @elseif($isContact)
            <p>Have a question about a course or your payment? Our team is happy to help.</p>
        @endif
    </div>
</section>

<section class="page-body">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="page-card">
                    @if($banner)<img src="{{ $banner }}" alt="{{ $page->name }}" class="page-banner">@endif

                    @if($isContact)
                        <div class="contact-grid">
                            @if($site['phone'])
                                <a href="tel:{{ Storefront::tel() }}" class="contact-tile"><span class="contact-tile__icon"><i class="bi bi-telephone"></i></span><span><small>Call us</small><strong>{{ $site['phone'] }}</strong></span></a>
                                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="contact-tile"><span class="contact-tile__icon" style="background:#25D366"><i class="bi bi-whatsapp"></i></span><span><small>WhatsApp</small><strong>Chat with us</strong></span></a>
                            @endif
                            @if($site['email'])
                                <a href="mailto:{{ $site['email'] }}" class="contact-tile"><span class="contact-tile__icon" style="background:#EA580C"><i class="bi bi-envelope"></i></span><span><small>Email</small><strong>{{ $site['email'] }}</strong></span></a>
                            @endif
                            @isset($site['social']['facebook'])
                                <a href="{{ $site['social']['facebook'] }}" target="_blank" rel="noopener" class="contact-tile"><span class="contact-tile__icon" style="background:#1877F2"><i class="bi bi-messenger"></i></span><span><small>Facebook</small><strong>Message our page</strong></span></a>
                            @endisset
                            <div class="contact-tile"><span class="contact-tile__icon" style="background:#0F172A"><i class="bi bi-geo-alt"></i></span><span><small>Address</small><strong>{{ $site['address'] }}</strong></span></div>
                            <div class="contact-tile"><span class="contact-tile__icon" style="background:#12B76A"><i class="bi bi-clock"></i></span><span><small>Support hours</small><strong>Sat – Thu, 10am – 8pm</strong></span></div>
                        </div>
                    @endif

                    @if(trim(strip_tags((string) $page->description)))
                        <div class="cms-content">{!! clean_html($page->description) !!}</div>
                    @elseif(!$isContact)
                        <p class="text-muted mb-0">This page is being updated. Please check back soon.</p>
                    @endif
                </article>
            </div>

            <aside class="col-lg-4">
                @if($footerPages->count())
                    <div class="side-card">
                        <h6>Information</h6>
                        <ul class="side-links">
                            @foreach($footerPages as $fp)
                                <li><a href="{{ route('pageView', $fp->slug) }}" class="{{ $fp->id === $page->id ? 'is-active' : '' }}">{{ $fp->name }} <i class="bi bi-chevron-right"></i></a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="side-card side-cta">
                    <h5>Ready to start learning?</h5>
                    <p class="mb-3">Explore practical courses taught in Bangla, or start with a free course today.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('courses.index') }}" class="btn btn-light">Browse Courses</a>
                        <a href="{{ route('courses.free') }}" class="btn btn-outline-light">Watch Free Courses</a>
                    </div>
                </div>
                @if(!$isContact && $site['phone'])
                    <div class="side-card">
                        <h6>Need help?</h6>
                        <a href="tel:{{ Storefront::tel() }}" class="contact-tile"><span class="contact-tile__icon"><i class="bi bi-headset"></i></span><span><small>Call our team</small><strong>{{ $site['phone'] }}</strong></span></a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>
@endsection
