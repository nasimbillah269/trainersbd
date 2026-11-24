@php($site = \App\Support\Storefront::site())
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $site['name'] . ' | ' . $site['tagline'])</title>
    <meta name="description" content="@yield('meta_description', $site['meta_description'])">
    <meta name="keywords" content="@yield('meta_keywords', $site['meta_keywords'])">
    <meta property="og:title" content="@yield('title', $site['name'])">
    <meta property="og:description" content="@yield('meta_description', $site['tagline'])">
    <meta name="theme-color" content="#003080">
    <link rel="icon" type="image/png" href="{{ asset(general()->favicon()) }}">
    <link rel="apple-touch-icon" href="{{ asset(general()->favicon()) }}">
    <meta property="og:image" content="{{ asset('frontend/images/brand/trainersbd-banner.png') }}">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/css/app.css') }}?v=15">
    @stack('css')
</head>
<body class="@yield('body_class')">

    @include('frontend.partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('frontend.partials.footer')

    {{-- YouTube lesson player (free courses & previews) --}}
    <div class="modal fade video-modal" id="videoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <span class="video-modal__eyebrow">Now playing</span>
                        <h5 class="modal-title" id="videoModalTitle">Lesson</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9" id="videoModalFrame"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-stack" id="toastStack" aria-live="polite"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js"></script>
    <script>
        window.TBD = {
            routes: {
                cart: @json(route('cart')),
                checkout: @json(route('checkout')),
                checkoutPlace: @json(route('checkout.place')),
                cartSync: @json(route('cart.sync')),
                home: @json(route('index')),
            },
            csrf: @json(csrf_token())
        };
    </script>
    <script src="{{ asset('frontend/js/app.js') }}?v=5"></script>
    @stack('js')
</body>
</html>
