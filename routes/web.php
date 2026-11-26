<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\SiteController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Welcome\WelcomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Admin\MenusController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\EcommerceController;
use App\Http\Controllers\Admin\CourseAdminController;
use App\Http\Controllers\Admin\CourseOrderController;
use App\Http\Controllers\Admin\CourseReportController;

/*
|--------------------------------------------------------------------------
| TrainersBD storefront
|--------------------------------------------------------------------------
*/
Route::get('/', [SiteController::class, 'home'])->name('index');
Route::get('/courses', [SiteController::class, 'courses'])->name('courses.index');
Route::get('/courses/category/{slug}', [SiteController::class, 'coursesCategory'])->name('courses.category');
Route::get('/free-courses', [SiteController::class, 'freeCourses'])->name('courses.free');
Route::get('/course/{slug}', [SiteController::class, 'course'])->name('course.show');
Route::get('/cart', [SiteController::class, 'cart'])->name('cart');
Route::get('/checkout', [SiteController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [SiteController::class, 'placeOrder'])->middleware('throttle:8,1')->name('checkout.place');
Route::get('/order/{invoice}/success', [SiteController::class, 'orderSuccess'])->middleware('signed')->name('order.success');
Route::post('/cart/sync', [SiteController::class, 'cartSync'])->middleware('throttle:60,1')->name('cart.sync');
Route::post('/course/{slug}/review', [SiteController::class, 'review'])->middleware('throttle:5,1')->name('course.review');

// Image helpers used by the admin panel
Route::get('/image-view', [WelcomeController::class, 'imageView'])->name('imageView');
Route::get('/image/{template?}/{image?}', [WelcomeController::class, 'imageView2'])->name('imageView2');

/*
| Legacy route names: the old storefront has been removed, but the admin
| panel and auth controller still generate these URLs. They redirect to the
| new storefront until those screens are rebuilt.
*/
Route::get('/product/{slug}', [SiteController::class, 'legacyProduct'])->name('productView');
Route::get('/blog/category/{slug}', [SiteController::class, 'legacyHome'])->name('blogCategory');
Route::get('/blog/{slug}', [SiteController::class, 'legacyHome'])->name('blogView');
Route::get('/carts', [SiteController::class, 'legacyHome'])->name('carts');

// Student account (accounts are created at checkout)
Route::get('/account/login', [AccountController::class, 'login'])->name('customer.login');
Route::post('/account/login', [AccountController::class, 'login'])->middleware('throttle:10,1')->name('customer.login.post');
Route::middleware('auth')->group(function () {
    Route::get('/customer/dashboard', [AccountController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/my-courses/{slug}', [AccountController::class, 'learn'])->name('customer.learn');
    Route::post('/customer/password', [AccountController::class, 'password'])->middleware('throttle:6,1')->name('customer.password');
    Route::post('/account/logout', [AccountController::class, 'logout'])->name('customer.logout');
});
Route::redirect('/my-courses', '/customer/dashboard');

// Admin authentication (students do not log in)
Route::any('/admin-login', [AuthController::class, 'admin'])->name('admin');
Route::redirect('/login', '/admin-login')->name('login');
Route::post('/log-out', [AuthController::class, 'logout'])->name('logout');

// Admin Route Group Start

Route::group(['prefix'=>'admin', 'as'=>'admin.','middleware'=>['auth','role:admin','permission']], function(){


Route::get('/dashboard', [CourseOrderController::class, 'dashboard'])->name('dashboard');

// ---------- TrainersBD: courses ----------
Route::get('/courses', [CourseAdminController::class, 'index'])->name('courses.index');
Route::get('/free-courses', [CourseAdminController::class, 'freeIndex'])->name('free-courses.index');
Route::get('/courses/create', [CourseAdminController::class, 'create'])->name('courses.create');
Route::post('/courses', [CourseAdminController::class, 'store'])->name('courses.store');
Route::get('/courses/{course}/edit', [CourseAdminController::class, 'edit'])->name('courses.edit');
Route::post('/courses/{course}', [CourseAdminController::class, 'update'])->name('courses.update');
Route::post('/courses/{course}/delete', [CourseAdminController::class, 'destroy'])->name('courses.destroy');
Route::post('/courses/{course}/duplicate', [CourseAdminController::class, 'duplicate'])->name('courses.duplicate');
Route::post('/courses/{course}/quick', [CourseAdminController::class, 'quickUpdate'])->name('courses.quick');

Route::get('/course-categories', [CourseAdminController::class, 'categories'])->name('course-categories.index');
Route::post('/course-categories', [CourseAdminController::class, 'categorySave'])->name('course-categories.store');
Route::post('/course-categories/{category}', [CourseAdminController::class, 'categorySave'])->name('course-categories.update');
Route::post('/course-categories/{category}/delete', [CourseAdminController::class, 'categoryDelete'])->name('course-categories.destroy');

Route::get('/instructors', [CourseAdminController::class, 'instructors'])->name('instructors.index');
Route::post('/instructors', [CourseAdminController::class, 'instructorSave'])->name('instructors.store');
Route::post('/instructors/{instructor}', [CourseAdminController::class, 'instructorSave'])->name('instructors.update');
Route::post('/instructors/{instructor}/delete', [CourseAdminController::class, 'instructorDelete'])->name('instructors.destroy');

Route::get('/course-reviews', [CourseAdminController::class, 'reviews'])->name('course-reviews.index');
Route::post('/course-reviews/{review}/status', [CourseAdminController::class, 'reviewStatus'])->name('course-reviews.status');
Route::post('/course-reviews/{review}/delete', [CourseAdminController::class, 'reviewDelete'])->name('course-reviews.destroy');

// ---------- TrainersBD: orders & payments ----------
Route::get('/course-orders', [CourseOrderController::class, 'index'])->name('course-orders.index');
Route::get('/course-orders/{order}', [CourseOrderController::class, 'show'])->name('course-orders.show');
Route::post('/course-orders/{order}', [CourseOrderController::class, 'update'])->name('course-orders.update');
Route::post('/course-orders/{order}/quick', [CourseOrderController::class, 'quick'])->name('course-orders.quick');
Route::post('/course-orders/{order}/delete', [CourseOrderController::class, 'destroy'])->name('course-orders.destroy');
Route::get('/students', [CourseOrderController::class, 'students'])->name('students.index');
Route::get('/students/export', [CourseOrderController::class, 'exportStudents'])->name('students.export');
Route::get('/payment-settings', [CourseOrderController::class, 'paymentSettings'])->name('payment-settings');
Route::post('/payment-settings', [CourseOrderController::class, 'paymentSettingsSave'])->name('payment-settings.save');
Route::any('/my-profile',[AdminController::class,'myProfile'])->name('myProfile');

// Medias Library Route
Route::get('/medias',[AdminController::class,'medies'])->name('medies');
Route::post('/medias/create',[AdminController::class,'mediesCreate'])->name('mediesCreate');
Route::match(['get','post'],'/medias/edit/{id}',[AdminController::class,'mediesEdit'])->name('mediesEdit');
Route::get('/medies/delete/{id}',[AdminController::class,'mediesDelete'])->name('mediesDelete');
// Medias Library Route End

//Page Management
Route::get('/pages',[AdminController::class,'pages'])->name('pages');
Route::any('/pages/{action}/{id?}',[AdminController::class,'pagesAction'])->name('pagesAction');
//Page Management End

// Posts Comments Route End
Route::get('/posts/comments/all',[PostsController::class,'postsCommentsAll'])->name('postsCommentsAll');
Route::get('/posts/comments/post/{id}',[PostsController::class,'postsComments'])->name('postsComments');
Route::any('/posts/comments/{action}/{id}',[PostsController::class,'postsCommentsAction'])->name('postsCommentsAction');
// Posts Comments Route End

// Posts Categories Route
Route::get('/posts/categories',[PostsController::class,'postsCategories'])->name('postsCategories');
Route::any('/posts/categories/{action}/{id?}',[PostsController::class,'postsCategoriesAction'])->name('postsCategoriesAction');
// Posts Categories Route End

// Posts Tags Route
Route::get('/posts/tags',[PostsController::class,'postsTags'])->name('postsTags');
Route::any('/posts/tags/{action}/{id?}',[PostsController::class,'postsTagsAction'])->name('postsTagsAction');
// Posts Tags Route End

// Posts Route
Route::get('/posts',[PostsController::class,'posts'])->name('posts');
Route::any('/posts/{action}/{id?}',[PostsController::class,'postsAction'])->name('postsAction');
// Posts Route End

//Ecommerce Mangement Start


// Products Categories Route
Route::get('/products/categories',[EcommerceController::class,'productsCategories'])->name('productsCategories');
Route::any('/products/categories/{action}/{id?}',[EcommerceController::class,'productsCategoriesAction'])->name('productsCategoriesAction');
// Products Categories Route End

// Products Tags Route
Route::get('/products/tags',[EcommerceController::class,'productsTags'])->name('productsTags');
Route::any('/products/tags/{action}/{id?}',[EcommerceController::class,'productsTagsAction'])->name('productsTagsAction');
// Products Tags Route End

// Products Countries Route
Route::get('/products/countries',[EcommerceController::class,'productsCountries'])->name('productsCountries');
Route::any('/products/countries/{action}/{id?}',[EcommerceController::class,'productsCountriesAction'])->name('productsCountriesAction');
// Products Countries Route End

// Products Attributes Route
Route::get('/products/attributes',[EcommerceController::class,'productsAttributes'])->name('productsAttributes');
Route::any('/products/attributes/{action}/{id?}',[EcommerceController::class,'productsAttributesAction'])->name('productsAttributesAction');
Route::any('/products/attributes/item/{action}/{id}',[EcommerceController::class,'productsAttributesItemAction'])->name('productsAttributesItemAction');
// Products Attributes Route End


//Products Management
Route::get('/products',[EcommerceController::class,'products'])->name('products');
Route::any('/products/{action}/{id?}',[EcommerceController::class,'productsAction'])->name('productsAction');
Route::any('/products/update/ajax/{column}/{id}',[EcommerceController::class,'productsUpdateAjax'])->name('productsUpdateAjax');
//Products Management End

// Ecommerce Setting Route
Route::get('/ecommerce/coupons',[EcommerceController::class,'ecommerceCoupons'])->name('ecommerceCoupons');
Route::any('/ecommerce/coupons/{action}/{id?}',[EcommerceController::class,'ecommerceCouponsAction'])->name('ecommerceCouponsAction');
Route::any('/ecommerce/setting/{action}/{id?}',[EcommerceController::class,'ecommerceSetting'])->name('ecommerceSetting');
// Ecommerce Setting Route End

// Order Management Route End
Route::get('/invoice/{id}',[OrdersController::class,'invoice'])->name('invoice');
Route::get('/orders/{status?}',[OrdersController::class,'orders'])->name('orders');
Route::any('/orders-manage/{action}/{id}',[OrdersController::class,'ordersAction'])->name('ordersAction');
Route::get('/orders/manage/{id}',[OrdersController::class,'ordersManage'])->name('ordersManage');
Route::post('/orders/update/{id}',[OrdersController::class,'ordersManageUpdate'])->name('ordersManageUpdate');
Route::post('/orders/payments/{id}',[OrdersController::class,'ordersPaymentsUpdate'])->name('ordersPaymentsUpdate');
Route::post('/orders/return/{id}',[OrdersController::class,'ordersReturnUpdate'])->name('ordersReturnUpdate');
Route::post('/orders/item/update/{itemId}',[OrdersController::class,'orderItemUpdate'])->name('orderItemUpdate');
// Order Management Route End

// Accounts History Start
Route::any('/accounts/method/{action?}/{id?}',[AdminController::class,'accountsMethod'])->name('accountsMethod');


// Reports Route Start
Route::get('/reports/{type}',[CourseReportController::class,'show'])->name('reportsAll');
// Reports Route End

// Clients Route
Route::get('/clients',[AdminController::class,'clients'])->name('clients');
Route::any('/clients/{action}/{id?}',[AdminController::class,'clientsAction'])->name('clientsAction');
// Clients Route End

// Brands Route
Route::get('/brands',[AdminController::class,'brands'])->name('brands');
Route::any('/brands/{action}/{id?}',[AdminController::class,'brandsAction'])->name('brandsAction');
// Brands Route End

// Slider Route
Route::get('/sliders',[AdminController::class,'sliders'])->name('sliders');
Route::any('/sliders/{action}/{id?}',[AdminController::class,'slidersAction'])->name('slidersAction');
Route::any('/sliders/slide/{action}/{id}',[AdminController::class,'slideAction'])->name('slideAction');
// Slider Route End

// Gallery Route
Route::get('/galleries',[AdminController::class,'galleries'])->name('galleries');
Route::any('/galleries/{action}/{id?}',[AdminController::class,'galleriesAction'])->name('galleriesAction');
// Gallery Route End


// Theme Route
Route::get('/theme-setting',[AdminController::class,'themeSetting'])->name('themeSetting');
Route::any('/theme-setting/{action}/{id?}',[AdminController::class,'themeSettingAction'])->name('themeSettingAction');
// Theme Route End

// Menus Route
Route::get('/menus',[MenusController::class,'menus'])->name('menus');
Route::any('/menus/config/{action}/{id?}',[MenusController::class,'menusAction'])->name('menusAction');
Route::post('/menus/items/post/{id}',[MenusController::class,'menusItemsPost'])->name('menusItemsPost');
Route::any('/menus/items/{action}/{id}',[MenusController::class,'menusItemsAction'])->name('menusItemsAction');
// Menus Route End


//User Management
Route::get('/users/admin/',[AdminController::class,'usersAdmin'])->name('usersAdmin');
Route::any('/users/admin/{action}/{id?}',[AdminController::class,'usersAdminAction'])->name('usersAdminAction');

Route::get('/users/customer/',[AdminController::class,'usersCustomer'])->name('usersCustomer');
Route::any('/users/customer/{action}/{id?}',[AdminController::class,'usersCustomerAction'])->name('usersCustomerAction');

Route::get('/users/roles',[AdminController::class,'userRoles'])->name('userRoles');
Route::any('/users/roles/{action}/{id?}',[AdminController::class,'userRoleAction'])->name('userRoleAction');

Route::get('/subscribes',[AdminController::class,'subscribes'])->name('subscribes');

// Apps Setting
Route::get('/setting/{type}',[AdminController::class,'setting'])->name('setting');
Route::post('/setting/{type}/update',[AdminController::class,'settingUpdate'])->name('settingUpdate');

});


/*
| CMS pages from Admin → Pages (About Us, Contact Us, policies…).
| Keep this LAST so it never shadows another route.
*/
Route::get('/{slug}', [SiteController::class, 'page'])
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('pageView');
