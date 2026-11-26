@php
    $site = \App\Support\Storefront::site();
    $tel  = \App\Support\Storefront::tel();
    $student = auth()->check() && !auth()->user()->admin;
    $socialIcons = ['facebook' => 'bi-facebook', 'youtube' => 'bi-youtube', 'linkedin' => 'bi-linkedin', 'instagram' => 'bi-instagram'];

    // Header menu from Admin → Menus Setting ("Header Menus" location), with a sensible fallback
    $menu = \App\Support\Storefront::menu('Header Menus');
    if (!$menu) {
        $menu = [
            ['label' => 'Home', 'url' => route('index'), 'target' => null, 'active' => request()->routeIs('index'), 'children' => []],
            ['label' => 'Courses', 'url' => route('courses.index'), 'target' => null, 'active' => request()->routeIs('courses.*'), 'children' => []],
            ['label' => 'Free Courses', 'url' => route('courses.free'), 'target' => null, 'active' => request()->routeIs('courses.free'), 'children' => []],
        ];
    }
@endphp
<div class="topbar">
    <div class="container topbar__inner">
        <div class="topbar__info">
            @if($site['phone'])
                <a href="tel:{{ $tel }}" class="topbar__chip"><span class="topbar__icon"><i class="bi bi-telephone-fill"></i></span><span>{{ $site['phone'] }}</span></a>
            @endif
            @if($site['email'])
                <a href="mailto:{{ $site['email'] }}" class="topbar__chip topbar__chip--email" title="{{ $site['email'] }}"><span class="topbar__icon"><i class="bi bi-envelope-fill"></i></span><span>{{ $site['email'] }}</span></a>
            @endif
        </div>

        <a href="{{ route('courses.index') }}" class="topbar__note d-none d-lg-inline-flex">
            <span class="topbar__dot"></span>
            <span>Learn in Bangla &middot; Pay with <strong>bKash</strong>, <strong>Nagad</strong> or bank</span>
            <i class="bi bi-arrow-right"></i>
        </a>

        @if($site['social'])
            <div class="topbar__social">
                <span class="topbar__follow d-none d-sm-inline">Follow us</span>
                @foreach($site['social'] as $net => $link)
                    <a href="{{ $link }}" class="topbar__soc topbar__soc--{{ $net }}" aria-label="{{ ucfirst($net) }}" title="{{ ucfirst($net) }}" target="_blank" rel="noopener"><i class="bi {{ $socialIcons[$net] }}"></i></a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<header class="site-header" id="siteHeader">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="brand" href="{{ route('index') }}">
                <img src="{{ asset(general()->logo()) }}" alt="{{ $site['name'] }}" class="brand__logo" width="175" height="82">
            </a>

            <div class="d-flex align-items-center gap-2 order-lg-3">
                <a href="{{ route('cart') }}" class="icon-btn cart-btn" aria-label="Cart">
                    <i class="bi bi-bag"></i>
                    <span class="cart-count" data-cart-count hidden>0</span>
                </a>
                <a href="{{ route('courses.index') }}" class="btn btn-accent btn-cta d-none d-sm-inline-flex">
                    <span class="btn-cta__icon"><i class="bi bi-grid-fill"></i></span>
                    <span>Browse Courses</span>
                    <i class="bi bi-arrow-right btn-cta__arrow"></i>
                </a>
                <button class="navbar-toggler icon-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="mainNav">
                <ul class="navbar-nav mx-auto main-menu">
                    @foreach($menu as $item)
                        @if($item['children'])
                            @php($isMega = count($item['children']) > 5 || collect($item['children'])->contains(fn ($c) => $c['icon']))
                            <li class="nav-item has-sub {{ $isMega ? 'has-mega' : '' }}">
                                <a class="nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}" @if($item['target']) target="_blank" rel="noopener" @endif aria-haspopup="true">
                                    {{ $item['label'] }} <i class="bi bi-chevron-down nav-caret"></i>
                                </a>
                                <div class="sub-menu {{ $isMega ? 'sub-menu--mega' : '' }}">
                                    @foreach($item['children'] as $child)
                                        <div class="sub-menu__item {{ $child['children'] ? 'has-sub' : '' }}">
                                            <a href="{{ $child['url'] }}" class="sub-menu__link {{ $child['active'] ? 'is-active' : '' }}" @if($child['target']) target="_blank" rel="noopener" @endif>
                                                @if($child['icon'])<span class="sub-menu__icon"><i class="bi {{ $child['icon'] }}"></i></span>@endif
                                                <span class="sub-menu__text">
                                                    <strong>{{ $child['label'] }}</strong>
                                                    @if(!is_null($child['count']))<small>{{ $child['count'] }} {{ \Illuminate\Support\Str::plural('course', $child['count']) }}</small>@endif
                                                </span>
                                                @if($child['children'])<i class="bi bi-chevron-right sub-menu__caret"></i>@endif
                                            </a>
                                            @if($child['children'])
                                                <div class="sub-menu sub-menu--side">
                                                    @foreach($child['children'] as $g)
                                                        <a href="{{ $g['url'] }}" class="sub-menu__link {{ $g['active'] ? 'is-active' : '' }}" @if($g['target']) target="_blank" rel="noopener" @endif><span class="sub-menu__text"><strong>{{ $g['label'] }}</strong></span></a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($isMega)
                                        <a href="{{ $item['url'] }}" class="sub-menu__all">View all {{ \Illuminate\Support\Str::lower($item['label']) }} <i class="bi bi-arrow-right"></i></a>
                                    @endif
                                </div>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}" @if($item['target']) target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="offcanvas offcanvas-end mobile-nav" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <a class="brand" href="{{ route('index') }}" id="mobileNavLabel">
            <img src="{{ asset(general()->logo()) }}" alt="{{ $site['name'] }}" class="brand__logo" width="175" height="82">
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" data-lenis-prevent>
        <form class="search-pill mb-4" action="{{ route('courses.index') }}" method="get">
            <i class="bi bi-search"></i>
            <input type="search" name="q" placeholder="Search courses" aria-label="Search courses">
        </form>
        <nav class="mobile-nav__links">
            @foreach($menu as $i => $item)
                @if($item['children'])
                    <div class="mobile-nav__group">
                        <div class="mobile-nav__row">
                            <a href="{{ $item['url'] }}" class="{{ $item['active'] ? 'is-active' : '' }}">{{ $item['label'] }}</a>
                            <button type="button" class="mobile-nav__toggle {{ $item['active'] ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#mSub{{ $i }}" aria-expanded="{{ $item['active'] ? 'true' : 'false' }}" aria-label="Show {{ $item['label'] }} submenu"><i class="bi bi-chevron-down"></i></button>
                        </div>
                        <div class="collapse {{ $item['active'] ? 'show' : '' }}" id="mSub{{ $i }}">
                            <div class="mobile-nav__sub">
                                @foreach($item['children'] as $child)
                                    <a href="{{ $child['url'] }}" class="{{ $child['active'] ? 'is-active' : '' }}">@if($child['icon'])<i class="bi {{ $child['icon'] }}"></i>@endif {{ $child['label'] }}</a>
                                    @foreach($child['children'] as $g)
                                        <a href="{{ $g['url'] }}" class="mobile-nav__sub-2">{{ $g['label'] }}</a>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}" class="{{ $item['active'] ? 'is-active' : '' }}" @if($item['target']) target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a>
                @endif
            @endforeach
            <a href="{{ route('cart') }}"><i class="bi bi-bag"></i> My Cart</a>
            @if($student)
                <a href="{{ route('customer.dashboard') }}"><i class="bi bi-person-circle"></i> My Courses</a>
            @endif
        </nav>
        @if($site['phone'] || $site['email'])
            <div class="mobile-nav__contact">
                <p class="mobile-nav__contact-title">Need help choosing a course?</p>
                @if($site['phone'])
                    <a href="tel:{{ $tel }}" class="mobile-nav__contact-item">
                        <span class="mobile-nav__contact-icon"><i class="bi bi-telephone-fill"></i></span>
                        <span class="mobile-nav__contact-text"><small>Call us</small><strong>{{ $site['phone'] }}</strong></span>
                        <i class="bi bi-chevron-right mobile-nav__contact-arrow"></i>
                    </a>
                @endif
                @if($site['email'])
                    <a href="mailto:{{ $site['email'] }}" class="mobile-nav__contact-item">
                        <span class="mobile-nav__contact-icon mobile-nav__contact-icon--mail"><i class="bi bi-envelope-fill"></i></span>
                        <span class="mobile-nav__contact-text"><small>Email us</small><strong>{{ $site['email'] }}</strong></span>
                        <i class="bi bi-chevron-right mobile-nav__contact-arrow"></i>
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
