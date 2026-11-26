<?php
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
?>
<div class="topbar">
    <div class="container topbar__inner">
        <div class="topbar__info">
            <?php if($site['phone']): ?>
                <a href="tel:<?php echo e($tel); ?>" class="topbar__chip"><span class="topbar__icon"><i class="bi bi-telephone-fill"></i></span><span><?php echo e($site['phone']); ?></span></a>
            <?php endif; ?>
            <?php if($site['email']): ?>
                <a href="mailto:<?php echo e($site['email']); ?>" class="topbar__chip topbar__chip--email" title="<?php echo e($site['email']); ?>"><span class="topbar__icon"><i class="bi bi-envelope-fill"></i></span><span><?php echo e($site['email']); ?></span></a>
            <?php endif; ?>
        </div>

        <a href="<?php echo e(route('courses.index')); ?>" class="topbar__note d-none d-lg-inline-flex">
            <span class="topbar__dot"></span>
            <span>Learn in Bangla &middot; Pay with <strong>bKash</strong>, <strong>Nagad</strong> or bank</span>
            <i class="bi bi-arrow-right"></i>
        </a>

        <?php if($site['social']): ?>
            <div class="topbar__social">
                <span class="topbar__follow d-none d-sm-inline">Follow us</span>
                <?php $__currentLoopData = $site['social']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $net => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($link); ?>" class="topbar__soc topbar__soc--<?php echo e($net); ?>" aria-label="<?php echo e(ucfirst($net)); ?>" title="<?php echo e(ucfirst($net)); ?>" target="_blank" rel="noopener"><i class="bi <?php echo e($socialIcons[$net]); ?>"></i></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<header class="site-header" id="siteHeader">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="brand" href="<?php echo e(route('index')); ?>">
                <img src="<?php echo e(asset(general()->logo())); ?>" alt="<?php echo e($site['name']); ?>" class="brand__logo" width="175" height="82">
            </a>

            <div class="d-flex align-items-center gap-2 order-lg-3">
                <a href="<?php echo e(route('cart')); ?>" class="icon-btn cart-btn" aria-label="Cart">
                    <i class="bi bi-bag"></i>
                    <span class="cart-count" data-cart-count hidden>0</span>
                </a>
                <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-accent btn-cta d-none d-sm-inline-flex">
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
                    <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($item['children']): ?>
                            <?php ($isMega = count($item['children']) > 5 || collect($item['children'])->contains(fn ($c) => $c['icon'])); ?>
                            <li class="nav-item has-sub <?php echo e($isMega ? 'has-mega' : ''); ?>">
                                <a class="nav-link <?php echo e($item['active'] ? 'active' : ''); ?>" href="<?php echo e($item['url']); ?>" <?php if($item['target']): ?> target="_blank" rel="noopener" <?php endif; ?> aria-haspopup="true">
                                    <?php echo e($item['label']); ?> <i class="bi bi-chevron-down nav-caret"></i>
                                </a>
                                <div class="sub-menu <?php echo e($isMega ? 'sub-menu--mega' : ''); ?>">
                                    <?php $__currentLoopData = $item['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="sub-menu__item <?php echo e($child['children'] ? 'has-sub' : ''); ?>">
                                            <a href="<?php echo e($child['url']); ?>" class="sub-menu__link <?php echo e($child['active'] ? 'is-active' : ''); ?>" <?php if($child['target']): ?> target="_blank" rel="noopener" <?php endif; ?>>
                                                <?php if($child['icon']): ?><span class="sub-menu__icon"><i class="bi <?php echo e($child['icon']); ?>"></i></span><?php endif; ?>
                                                <span class="sub-menu__text">
                                                    <strong><?php echo e($child['label']); ?></strong>
                                                    <?php if(!is_null($child['count'])): ?><small><?php echo e($child['count']); ?> <?php echo e(\Illuminate\Support\Str::plural('course', $child['count'])); ?></small><?php endif; ?>
                                                </span>
                                                <?php if($child['children']): ?><i class="bi bi-chevron-right sub-menu__caret"></i><?php endif; ?>
                                            </a>
                                            <?php if($child['children']): ?>
                                                <div class="sub-menu sub-menu--side">
                                                    <?php $__currentLoopData = $child['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <a href="<?php echo e($g['url']); ?>" class="sub-menu__link <?php echo e($g['active'] ? 'is-active' : ''); ?>" <?php if($g['target']): ?> target="_blank" rel="noopener" <?php endif; ?>><span class="sub-menu__text"><strong><?php echo e($g['label']); ?></strong></span></a>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($isMega): ?>
                                        <a href="<?php echo e($item['url']); ?>" class="sub-menu__all">View all <?php echo e(\Illuminate\Support\Str::lower($item['label'])); ?> <i class="bi bi-arrow-right"></i></a>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e($item['active'] ? 'active' : ''); ?>" href="<?php echo e($item['url']); ?>" <?php if($item['target']): ?> target="_blank" rel="noopener" <?php endif; ?>><?php echo e($item['label']); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="offcanvas offcanvas-end mobile-nav" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <a class="brand" href="<?php echo e(route('index')); ?>" id="mobileNavLabel">
            <img src="<?php echo e(asset(general()->logo())); ?>" alt="<?php echo e($site['name']); ?>" class="brand__logo" width="175" height="82">
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" data-lenis-prevent>
        <form class="search-pill mb-4" action="<?php echo e(route('courses.index')); ?>" method="get">
            <i class="bi bi-search"></i>
            <input type="search" name="q" placeholder="Search courses" aria-label="Search courses">
        </form>
        <nav class="mobile-nav__links">
            <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($item['children']): ?>
                    <div class="mobile-nav__group">
                        <div class="mobile-nav__row">
                            <a href="<?php echo e($item['url']); ?>" class="<?php echo e($item['active'] ? 'is-active' : ''); ?>"><?php echo e($item['label']); ?></a>
                            <button type="button" class="mobile-nav__toggle <?php echo e($item['active'] ? '' : 'collapsed'); ?>" data-bs-toggle="collapse" data-bs-target="#mSub<?php echo e($i); ?>" aria-expanded="<?php echo e($item['active'] ? 'true' : 'false'); ?>" aria-label="Show <?php echo e($item['label']); ?> submenu"><i class="bi bi-chevron-down"></i></button>
                        </div>
                        <div class="collapse <?php echo e($item['active'] ? 'show' : ''); ?>" id="mSub<?php echo e($i); ?>">
                            <div class="mobile-nav__sub">
                                <?php $__currentLoopData = $item['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($child['url']); ?>" class="<?php echo e($child['active'] ? 'is-active' : ''); ?>"><?php if($child['icon']): ?><i class="bi <?php echo e($child['icon']); ?>"></i><?php endif; ?> <?php echo e($child['label']); ?></a>
                                    <?php $__currentLoopData = $child['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e($g['url']); ?>" class="mobile-nav__sub-2"><?php echo e($g['label']); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e($item['url']); ?>" class="<?php echo e($item['active'] ? 'is-active' : ''); ?>" <?php if($item['target']): ?> target="_blank" rel="noopener" <?php endif; ?>><?php echo e($item['label']); ?></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('cart')); ?>"><i class="bi bi-bag"></i> My Cart</a>
            <?php if($student): ?>
                <a href="<?php echo e(route('customer.dashboard')); ?>"><i class="bi bi-person-circle"></i> My Courses</a>
            <?php endif; ?>
        </nav>
        <?php if($site['phone'] || $site['email']): ?>
            <div class="mobile-nav__contact">
                <p class="mobile-nav__contact-title">Need help choosing a course?</p>
                <?php if($site['phone']): ?>
                    <a href="tel:<?php echo e($tel); ?>" class="mobile-nav__contact-item">
                        <span class="mobile-nav__contact-icon"><i class="bi bi-telephone-fill"></i></span>
                        <span class="mobile-nav__contact-text"><small>Call us</small><strong><?php echo e($site['phone']); ?></strong></span>
                        <i class="bi bi-chevron-right mobile-nav__contact-arrow"></i>
                    </a>
                <?php endif; ?>
                <?php if($site['email']): ?>
                    <a href="mailto:<?php echo e($site['email']); ?>" class="mobile-nav__contact-item">
                        <span class="mobile-nav__contact-icon mobile-nav__contact-icon--mail"><i class="bi bi-envelope-fill"></i></span>
                        <span class="mobile-nav__contact-text"><small>Email us</small><strong><?php echo e($site['email']); ?></strong></span>
                        <i class="bi bi-chevron-right mobile-nav__contact-arrow"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/frontend/partials/header.blade.php ENDPATH**/ ?>