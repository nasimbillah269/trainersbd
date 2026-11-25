<?php
    $role = Auth::user()->permission;
    $perm = $role ? (json_decode($role->permission, true) ?: []) : [];
    $can  = fn ($group, $action = 'list') => isset($perm[$group][$action]);
    $pendingOrders  = \App\Models\CourseOrder::where('status', 'pending')->count();
    $pendingReviews = \App\Models\CourseReview::where('status', 'pending')->count();
    $is = fn (...$p) => request()->is(...$p);
    $routeCourse = request()->route('course');
    $onFree = request()->routeIs('admin.free-courses.index')
        || (request()->routeIs('admin.courses.create') && request('type') === 'free')
        || (request()->routeIs('admin.courses.edit') && $routeCourse instanceof \App\Models\Course && $routeCourse->type === 'free');
    $freeCount = \App\Models\Course::where('type', 'free')->count();
    $orderTab = request()->routeIs('admin.course-orders.index') ? (request('payment') === 'unverified' ? 'unverified' : (request('status') ?: 'all')) : null;

    // [label, icon, url, active, visible, badge]  or  ['group' => label, 'icon' => ..., 'items' => [...]]
    $sections = [
        'General' => [
            ['Dashboard', 'fa-solid fa-chart-pie', route('admin.dashboard'), request()->routeIs('admin.dashboard'), true, null],
            ['My Profile', 'fa-regular fa-circle-user', route('admin.myProfile'), $is('admin/my-profile'), true, null],
            ['group' => 'Posts', 'icon' => 'fa-regular fa-newspaper', 'items' => [
                ['All Posts', route('admin.posts'), $is('admin/posts') || ($is('admin/posts/*') && !$is('admin/posts/categories*', 'admin/posts/tags*', 'admin/posts/comments*')), $can('posts')],
                ['New Post', route('admin.postsAction', ['create']), false, $can('posts', 'add')],
                ['Categories', route('admin.postsCategories'), $is('admin/posts/categories*'), $can('postsOther', 'category')],
                ['Tags', route('admin.postsTags'), $is('admin/posts/tags*'), $can('postsOther', 'tags')],
                ['Comments', route('admin.postsCommentsAll'), $is('admin/posts/comments*'), $can('postsOther', 'comments')],
            ]],
            ['Pages', 'fa-regular fa-file-lines', route('admin.pages'), $is('admin/pages*'), $can('pages'), null],
            ['Media Library', 'fa-solid fa-photo-film', route('admin.medies'), $is('admin/medias*', 'admin/medies*'), $can('medies'), null],
        ],
        'Courses Unit' => [
            ['group' => 'Ecommerce Setting', 'icon' => 'fa-solid fa-sliders', 'items' => [
                ['General Setting', route('admin.ecommerceSetting', 'general'), $is('admin/ecommerce/setting*'), $can('ecommerceSetting', 'general')],
                ['Coupons', route('admin.ecommerceCoupons'), $is('admin/ecommerce/coupons*'), $can('ecommerceSetting', 'coupons')],
                ['Payment Methods', route('admin.payment-settings'), request()->routeIs('admin.payment-settings*'), $can('appsSetting', 'general')],
            ]],
            ['group' => 'Courses', 'icon' => 'fa-solid fa-layer-group', 'items' => [
                ['All Courses', route('admin.courses.index'), !$onFree && request()->routeIs('admin.courses.index', 'admin.courses.edit'), $can('products')],
                ['Add Course', route('admin.courses.create'), !$onFree && request()->routeIs('admin.courses.create'), $can('products', 'add')],
                ['Categories', route('admin.course-categories.index'), request()->routeIs('admin.course-categories.*'), $can('products')],
                ['Instructors', route('admin.instructors.index'), request()->routeIs('admin.instructors.*'), $can('products')],
                ['Reviews', route('admin.course-reviews.index'), request()->routeIs('admin.course-reviews.*'), $can('products'), $pendingReviews],
                ['Tags', route('admin.productsTags'), $is('admin/products/tags*'), $can('postsOther', 'tag')],
                ['Countries', route('admin.productsCountries'), $is('admin/products/countries*'), $can('postsOther', 'country')],
                ['Attributes', route('admin.productsAttributes'), $is('admin/products/attributes*'), $can('postsOther', 'attribute')],
            ]],
            ['group' => 'Free Courses', 'icon' => 'fa-brands fa-youtube', 'badge' => null, 'items' => [
                ['All Free Courses', route('admin.free-courses.index'), $onFree && !request()->routeIs('admin.courses.create'), $can('products')],
                ['Add Free Course', route('admin.courses.create', ['type' => 'free']), $onFree && request()->routeIs('admin.courses.create'), $can('products', 'add')],
                ['View on Website', route('courses.free'), false, $can('products')],
            ]],
            ['group' => 'Order Management', 'icon' => 'fa-solid fa-receipt', 'badge' => $pendingOrders, 'items' => [
                ['Order List', route('admin.course-orders.index'), $orderTab === 'all' || request()->routeIs('admin.course-orders.show'), $can('orders')],
                ['Pending Orders', route('admin.course-orders.index', ['status' => 'pending']), $orderTab === 'pending', $can('orders'), $pendingOrders],
                ['Confirmed Orders', route('admin.course-orders.index', ['status' => 'confirmed']), $orderTab === 'confirmed', $can('orders')],
                ['On Hold Orders', route('admin.course-orders.index', ['status' => 'hold']), $orderTab === 'hold', $can('orders')],
                ['Cancelled Orders', route('admin.course-orders.index', ['status' => 'cancelled']), $orderTab === 'cancelled', $can('orders')],
                ['Unverified Payments', route('admin.course-orders.index', ['payment' => 'unverified']), $orderTab === 'unverified', $can('orders')],
                ['Students', route('admin.students.index'), request()->routeIs('admin.students.*'), $can('orders')],
            ]],
            ['group' => 'Reports Management', 'icon' => 'fa-solid fa-chart-line', 'items' => [
                ['Summary Reports', route('admin.reportsAll', 'summery'), $is('admin/reports/summery'), $can('reports', 'summery')],
                ['Products Reports', route('admin.reportsAll', 'products'), $is('admin/reports/products'), $can('reports', 'products')],
                ['Customer Reports', route('admin.reportsAll', 'customer-reports'), $is('admin/reports/customer-reports'), $can('reports', 'customer')],
                ['Order Reports', route('admin.reportsAll', 'order-reports'), $is('admin/reports/order-reports'), $can('reports', 'orders')],
            ]],
        ],
        'Website' => [
            ['Clients', 'fa-solid fa-handshake', route('admin.clients'), $is('admin/clients*'), $can('clients'), null],
            ['Brands', 'fa-solid fa-copyright', route('admin.brands'), $is('admin/brands*'), $can('brands'), null],
            ['Sliders', 'fa-regular fa-images', route('admin.sliders'), $is('admin/sliders*'), $can('sliders'), null],
            ['Galleries', 'fa-solid fa-table-cells-large', route('admin.galleries'), $is('admin/galleries*'), $can('galleries'), null],
            ['Menus Setting', 'fa-solid fa-bars', route('admin.menus'), $is('admin/menus*'), $can('menus'), null],
            ['Theme Setting', 'fa-solid fa-palette', route('admin.themeSetting'), $is('admin/theme-setting*'), $can('themeSetting'), null],
        ],
        'Users' => [
            ['Administrator Users', 'fa-solid fa-user-shield', route('admin.usersAdmin'), $is('admin/users/admin*'), $can('adminUsers'), null],
            ['Roles Users', 'fa-solid fa-key', route('admin.userRoles'), $is('admin/users/roles*'), $can('adminRoles'), null],
            ['Customer Users', 'fa-solid fa-users', route('admin.usersCustomer'), $is('admin/users/customer*'), $can('users'), null],
            ['Subscribe Users', 'fa-solid fa-envelope-open-text', route('admin.subscribes'), $is('admin/subscribes*'), $can('subscribe'), null],
        ],
        'Apps Setting' => [
            ['General Setting', 'fa-solid fa-gear', route('admin.setting', 'general'), $is('admin/setting/general*'), $can('appsSetting', 'general'), null],
            ['Mail Setting', 'fa-regular fa-envelope', route('admin.setting', 'mail'), $is('admin/setting/mail*'), $can('appsSetting', 'mail'), null],
            ['SMS Setting', 'fa-regular fa-comment-dots', route('admin.setting', 'sms'), $is('admin/setting/sms*'), $can('appsSetting', 'sms'), null],
            ['Social Setting', 'fa-solid fa-share-nodes', route('admin.setting', 'social'), $is('admin/setting/social*'), $can('appsSetting', 'social'), null],
        ],
    ];
?>
<aside class="adm-sidebar" aria-label="Admin navigation">
    <div class="adm-brand">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="adm-brand__link" title="<?php echo e(general()->title); ?> Admin">
            <span class="adm-brand__logo"><img src="<?php echo e(asset(general()->logo())); ?>" alt="<?php echo e(general()->title); ?>"></span>
            <span class="adm-brand__icon"><img src="<?php echo e(asset(general()->favicon())); ?>" alt=""></span>
        </a>
        <button type="button" class="adm-icon-btn adm-icon-btn--ghost d-xl-none" data-adm-close aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <nav class="adm-nav">
        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $heading => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $visible = array_values(array_filter(array_map(function ($i) {
                    if (isset($i['group'])) {
                        $i['items'] = array_values(array_filter($i['items'], fn ($c) => $c[3]));
                        return count($i['items']) ? $i : null;
                    }
                    return $i[4] ? $i : null;
                }, $items)));
            ?>
            <?php if(count($visible)): ?>
                <div class="adm-nav__heading"><?php echo e($heading); ?></div>
                <?php $__currentLoopData = $visible; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(isset($item['group'])): ?>
                        <?php ($open = collect($item['items'])->contains(fn ($c) => $c[2])); ?>
                        <div class="adm-nav__group <?php echo e($open ? 'is-open' : ''); ?>" data-nav-group>
                            <button type="button" class="adm-nav__link adm-nav__toggle <?php echo e($open ? 'is-parent-active' : ''); ?>" data-nav-toggle title="<?php echo e($item['group']); ?>" aria-expanded="<?php echo e($open ? 'true' : 'false'); ?>">
                                <i class="<?php echo e($item['icon']); ?>"></i>
                                <span><?php echo e($item['group']); ?></span>
                                <?php if(!empty($item['badge'])): ?><em class="adm-nav__badge"><?php echo e($item['badge']); ?></em><?php endif; ?>
                                <b class="adm-nav__caret fa-solid fa-chevron-down"></b>
                            </button>
                            <div class="adm-nav__sub">
                                <?php $__currentLoopData = $item['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($c[1]); ?>" class="adm-nav__sublink <?php echo e($c[2] ? 'is-active' : ''); ?>">
                                        <span><?php echo e($c[0]); ?></span>
                                        <?php if(!empty($c[4])): ?><em class="adm-nav__badge"><?php echo e($c[4]); ?></em><?php endif; ?>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php ([$label, $icon, $url, $active, $show, $badge] = $item); ?>
                        <a href="<?php echo e($url); ?>" class="adm-nav__link <?php echo e($active ? 'is-active' : ''); ?>" title="<?php echo e($label); ?>">
                            <i class="<?php echo e($icon); ?>"></i>
                            <span><?php echo e($label); ?></span>
                            <?php if($badge): ?><em class="adm-nav__badge"><?php echo e($badge); ?></em><?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <div class="adm-sidebar__foot">
        <a href="<?php echo e(route('index')); ?>" target="_blank" class="adm-sidebar__site">
            <i class="fa-solid fa-globe"></i>
            <span><strong>View website</strong><small><?php echo e(parse_url(config('app.url'), PHP_URL_HOST) ?: 'trainersbd.com'); ?></small></span>
            <i class="fa-solid fa-arrow-up-right-from-square ml-auto"></i>
        </a>
    </div>
</aside>
<?php /**PATH D:\xampp\htdocs\trainersbd\resources\views/admin/layouts/sidebar.blade.php ENDPATH**/ ?>