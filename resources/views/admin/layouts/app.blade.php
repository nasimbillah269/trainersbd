<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    @hasSection('title')
        @yield('title')
    @else
        <title>Admin | {{ general()->title }}</title>
    @endif
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset(general()->favicon()) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Siliguri:wght@500;600&display=swap" rel="stylesheet">

    {{-- Base theme (kept so existing admin screens and plugins keep working) --}}
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/css/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/css/components.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/css/tag-editor.css') }}">
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/css/style.css') }}">
    <script src="{{ asset(assetLinkAdmin() . '/app-assets/js/JsBarcode.all.js') }}"></script>

    {{-- TrainersBD admin design --}}
    <link rel="stylesheet" href="{{ asset(assetLinkAdmin() . '/app-assets/css/premium.css') }}?v=4">
    @stack('css')
</head>
<body class="adm">
    <div class="adm-shell">
        @include('admin.layouts.sidebar')
        <div class="adm-backdrop" data-adm-close></div>

        <div class="adm-main">
            @include('admin.layouts.header')
            <main class="adm-content">
                <div class="content-wrapper">
                    @yield('contents')
                </div>
            </main>
            @include('admin.layouts.footer')
        </div>
    </div>

    <div class="adm-toasts" id="admToasts">
        @foreach(['success' => 'fa-circle-check', 'error' => 'fa-circle-exclamation', 'info' => 'fa-circle-info'] as $type => $icon)
            @if(session($type))
                <div class="adm-toast adm-toast--{{ $type }}"><i class="fa-solid {{ $icon }}"></i><span>{{ session($type) }}</span><button type="button" data-toast-close aria-label="Close">&times;</button></div>
            @endif
        @endforeach
    </div>

    <script src="{{ asset(assetLinkAdmin() . '/app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset(assetLinkAdmin() . '/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ asset(assetLinkAdmin() . '/app-assets/js/printThis.js') }}"></script>
    <script src="{{ asset(assetLinkAdmin() . '/app-assets/js/tag-editor.js') }}"></script>
    <script src="{{ asset(assetLinkAdmin() . '/app-assets/js/scripts/forms/select/form-select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script>
        (function () {
            // Sidebar toggle (mobile) and collapse (desktop)
            var body = document.body;
            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-adm-toggle]')) {
                    if (window.innerWidth < 1200) { body.classList.toggle('adm-nav-open'); }
                    else {
                        body.classList.toggle('adm-collapsed');
                        try { localStorage.setItem('admCollapsed', body.classList.contains('adm-collapsed') ? '1' : '0'); } catch (err) {}
                    }
                }
                if (e.target.closest('[data-adm-close]')) { body.classList.remove('adm-nav-open'); }
                var navToggle = e.target.closest('[data-nav-toggle]');
                if (navToggle) {
                    if (body.classList.contains('adm-collapsed')) { body.classList.remove('adm-collapsed'); try { localStorage.setItem('admCollapsed', '0'); } catch (err) {} }
                    var group = navToggle.closest('[data-nav-group]');
                    var open = group.classList.toggle('is-open');
                    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                }
                var close = e.target.closest('[data-toast-close]');
                if (close) { close.parentNode.remove(); }
                var confirmBtn = e.target.closest('[data-confirm]');
                if (confirmBtn && !window.confirm(confirmBtn.getAttribute('data-confirm'))) { e.preventDefault(); e.stopImmediatePropagation(); }
            }, true);
            try { if (localStorage.getItem('admCollapsed') === '1' && window.innerWidth >= 1200) { body.classList.add('adm-collapsed'); } } catch (err) {}
            setTimeout(function () {
                document.querySelectorAll('.adm-toast--success, .adm-toast--info').forEach(function (t) { t.classList.add('is-hiding'); setTimeout(function () { t.remove(); }, 400); });
            }, 5000);
        })();
    </script>
    @stack('js')
    @include('admin.layouts.scripts')
</body>
</html>
