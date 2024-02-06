<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Auth::routes();
// Home page routes
Route::get('/', [App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('web.home');
Route::post('/subscribe', [App\Http\Controllers\Frontend\HomeController::class, 'subscribe'])->name('web.subscribe');
Route::get('/pages/{slug}', [App\Http\Controllers\Frontend\PageController::class, 'getPageForSlug'])->name('web.home.pages');
Route::get('/header', [App\Http\Controllers\Frontend\HomeController::class, 'getHeaderContent'])->name('web.home.header');

// user register
Route::post('/user-register', [App\Http\Controllers\Frontend\RegisterController::class, 'userRegister'])->name('web.user.register');

// product 
Route::get('/shop', [App\Http\Controllers\Frontend\ProductController::class, 'shop'])->name('web.shop');
Route::get('/search', [App\Http\Controllers\Frontend\ProductController::class, 'searchProducts'])->name('web.product.search');
Route::post('/add-reviews', [App\Http\Controllers\Frontend\ProductController::class, 'saveProductReviews'])->name('web.product.reviews.add');
Route::get('/categories/{slug}', [App\Http\Controllers\Frontend\ProductController::class, 'getProductsForcategorySlug'])->name('web.category.view');
Route::get('/featured-product', [App\Http\Controllers\Frontend\ProductController::class, 'getFeatureProduct'])->name('web.home.featureProduct');
Route::post('/featured-product-variant', [App\Http\Controllers\Frontend\ProductController::class, 'getFeatureProductVariant'])->name('web.home.featureProductVariant');

// password management
Route::get('/forgot-password', [App\Http\Controllers\Frontend\AuthController::class, 'forgotPasswordUI'])->name('web.password.forgot');
Route::post('/forgot-password', [App\Http\Controllers\Frontend\AuthController::class, 'forgotPassword'])->name('web.password.reset');
Route::get('/reset-password/{token}', [App\Http\Controllers\Frontend\AuthController::class, 'resetPassword'])->name('web.password.resetUI');
Route::post('/reset-password', [App\Http\Controllers\Frontend\AuthController::class, 'changePassword'])->name('web.password.change');

// cart routes
Route::get('/cart',[App\Http\Controllers\Frontend\CartController::class,'cart'])->name('web.cart');
Route::get('front_cart_add',[App\Http\Controllers\Frontend\CartController::class,'addToCart'])->name('web.cart.add');
Route::get('front_minicart_remove',[App\Http\Controllers\Frontend\CartController::class,'miniCartRemove'])->name('web.cart.minicart.remove');
Route::get('front_cart_add_by_one',[App\Http\Controllers\Frontend\CartController::class,'cartAddByOne'])->name('web.cart.add.by.one');
Route::get('front_cart_remove_by_one',[App\Http\Controllers\Frontend\CartController::class,'cartRemoveByOne'])->name('web.cart.remove.by.one');
Route::get('cart/checkout',[App\Http\Controllers\Frontend\CartController::class,'checkout'])->name('web.checkout');
Route::post('cart/checkout',[App\Http\Controllers\Frontend\CartController::class,'addCheckoutAddresses'])->name('web.addCheckoutAddresses');
Route::get('/cart',[App\Http\Controllers\Frontend\CartController::class,'cart'])->name('web.cart');
Route::get('cart/proceed/checkout',[App\Http\Controllers\Frontend\CartController::class,'proceedToCheckout'])->name('web.proceed.checkout');
Route::post('cart/check-quantity',[App\Http\Controllers\Frontend\CartController::class,'checkProductQuantity'])->name('web.check.quantity');
Route::post('product/get-variant',[App\Http\Controllers\Frontend\CartController::class,'getVariantDataForId'])->name('web.variantData.get');
Route::post('cart/add-to-wishlist',[App\Http\Controllers\Frontend\CartController::class,'addToWishlist'])->name('web.addTo.wishlist');
Route::post('cart/remove-from-wishlist',[App\Http\Controllers\Frontend\CartController::class,'removeFromWishlist'])->name('web.removeFrom.wishlist');
Route::post('/cart',[App\Http\Controllers\Frontend\CartController::class,'applyCoupon'])->name('web.cart.coupon.add');
Route::post('/cart/coupon remove',[App\Http\Controllers\Frontend\CartController::class,'removeCoupon'])->name('web.cart.coupon.remove');
Route::post('/checkout/city/get',[App\Http\Controllers\Frontend\CartController::class,'getCityForCityName'])->name('web.checkout.city');
Route::post('/cart/get-quotation',[App\Http\Controllers\Frontend\CartController::class,'getCartQuotation'])->name('web.cart.quotation');

// User Profile
Route::get('/search', [App\Http\Controllers\Frontend\HomeController::class, 'mainSearch'])->name('web.main.search');
Route::get('/user/profile',[App\Http\Controllers\Frontend\UserController::class, 'getUserAccountDetails'])->name('web.user.account')->middleware(['auth']);

//category routes
Route::get('/shop/categories',[App\Http\Controllers\Frontend\CategoryController::class,'singlePageCategories'])->name('categories.view_category');
Route::get('/shop/{slug}',[App\Http\Controllers\Frontend\CategoryController::class, 'getProductsForCategory'])->name('front.category');

// user routes
Route::get('/user/profile',[App\Http\Controllers\Frontend\UserController::class, 'getUserAccountDetails'])->name('web.user.account')->middleware(['auth']);
Route::post('/user/addresses/edit',[App\Http\Controllers\Frontend\UserController::class, 'editUserAddresses'])->name('web.user.editAddress');
Route::post('/user/addresses/active',[App\Http\Controllers\Frontend\UserController::class, 'setActiveAddress'])->name('web.user.addressActiveStatus');
Route::post('/user/addresses/new',[App\Http\Controllers\Frontend\UserController::class, 'addNewAddress'])->name('web.user.addNewAddress');
Route::post('/user/update/profile',[App\Http\Controllers\Frontend\UserController::class, 'updateProfile'])->name('web.user.profileUpdate');
Route::get('/order/success/{id}',[App\Http\Controllers\Frontend\UserController::class, 'orderPlacingSucceeded'])->name('web.user.order.success');

// shop routes
Route::get('/shop/{slug}',[App\Http\Controllers\Frontend\ProductController::class, 'getProductsForCategory'])->name('web.category');
Route::get('/shop/products/{id}',[App\Http\Controllers\Frontend\ProductController::class, 'getProductForId'])->name('web.shop.product');
Route::get('/stock-clearance',[App\Http\Controllers\Frontend\ProductController::class, 'ShowStockClearanceProducts'])->name('web.stock.clearance');
Route::get('/search-suggest',[App\Http\Controllers\Frontend\ProductController::class, 'searchSuggest'])->name('web.search.suggest');

// order routes
Route::get('/orders/order_tracking/{trackingNumber}',[App\Http\Controllers\Frontend\OrderController::class, 'getOrderTracking'])->name('web.orders.order_tracking');
Route::post('/orders/store',[App\Http\Controllers\Frontend\OrderController::class, 'placeOrder'])->name('web.orders.store');

//contact-us
Route::get('/pages/contact-us',[App\Http\Controllers\Frontend\PageController::class, 'getContactUs'])->name('front.contactUs');
Route::post('/pages/contact-us',[App\Http\Controllers\Frontend\PageController::class, 'storeInquiry'])->name('front.storeInquiry');

// Expired promotion deactivate
Route::get('/deactivate-exp-promotions',[App\Http\Controllers\Frontend\ProductController::class, 'deactivateExpiredPromotions'])->name('web.promotions.expired.deactivate');

// About Us Page
Route::get('/about-us', [App\Http\Controllers\Frontend\PageController::class, 'LoadAboutUs'])->name('web.about.us');

// Blog Page
Route::get('/blog', [App\Http\Controllers\Frontend\PageController::class, 'LoadBlog'])->name('web.blog');

// Contact Us Page
Route::get('/contact-us', [App\Http\Controllers\Frontend\PageController::class, 'LoadContactUs'])->name('web.contact.us');
Route::post('/contact-submit', [App\Http\Controllers\Frontend\PageController::class, 'storeContactFormSubmit'])->name('web.contact.submit');

// Terms and Conditions Page
Route::get('/terms_and_conditions', [App\Http\Controllers\Frontend\PageController::class, 'LoadTermsAndConditions'])->name('web.terms.conditions');

// Refunds Page
Route::get('/refunds', [App\Http\Controllers\Frontend\PageController::class, 'LoadRefunds'])->name('web.refund');

// Cancellation Page
Route::get('/order-cancellation', [App\Http\Controllers\Frontend\PageController::class, 'LoadConcelation'])->name('web.concelation');

// Privacy Policy Page
Route::get('/privacy_policy', [App\Http\Controllers\Frontend\PageController::class, 'LoadPrivacyPolicy'])->name('web.privacy.policy');

// FAQ Page
Route::get('/faq', [App\Http\Controllers\Frontend\PageController::class, 'LoadFAQ'])->name('web.faq');

// Manufacturer Page
Route::get('/manufacturer/{id}', [App\Http\Controllers\Frontend\PageController::class, 'LoadManufacturer'])->name('web.manufacturer');

//search
Route::get('/autocomplete-search', [App\Http\Controllers\Frontend\HomeController::class, 'searchProduct'])->name('web.search');
Route::get('/search-single-product', [App\Http\Controllers\Frontend\HomeController::class, 'searchProduct'])->name('web.search.singleProduct');
