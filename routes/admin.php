<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use App\Mail\ForgotPasswordMail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/web', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    });
    // user management
    Route::get('/dashboard', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.all');
    Route::post('/users/edit', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.edit');
    Route::get('/users/status/{id}', [App\Http\Controllers\Admin\UserController::class, 'changeStatus'])->name('users.status');
    Route::get('/profile', [App\Http\Controllers\Admin\UserController::class, 'userProfileUI'])->name('profile');
    Route::post('/users/changePassword', [App\Http\Controllers\Admin\UserController::class, 'changeUserPassword'])->name('users.changePassword');
    Route::post('/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'updateUserPermissions'])->name('permissions.edit');
    Route::get('/permissions/add-new', [App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.addNew');
    Route::post('/permissions/create', [App\Http\Controllers\Admin\PermissionController::class, 'createPermissions'])->name('permissions.create');
    Route::get('/permissions/delete/{id}', [App\Http\Controllers\Admin\PermissionController::class, 'deletePermissions'])->name('permissions.delete');
    Route::post('/permissions/update', [App\Http\Controllers\Admin\PermissionController::class, 'updatePermissions'])->name('permissions.update');
    Route::get('/customers', [App\Http\Controllers\Admin\UserController::class, 'getAllCustomers'])->name('customers.all');


    //categories management
    Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.all');
    Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.create');
    Route::get('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'getCategoryForId'])->name('categories.get');
    Route::post('/categories/update', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.edit');
    Route::get('/categories/remove/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'remove'])->name('categories.remove');
    Route::get('/l1-sub-categories', [App\Http\Controllers\Admin\CategoryController::class, 'allSubCategoriesLevel1'])->name('subCategoriesL1.all');
    Route::get('/l2-sub-categories', [App\Http\Controllers\Admin\CategoryController::class, 'allSubCategoriesLevel2'])->name('subCategoriesL2.all');
    Route::post('/l2-sub-categories/update', [App\Http\Controllers\Admin\CategoryController::class, 'UpdateSubCategoriesLevel2'])->name('subCategoriesL2.edit');
    Route::post('/sub-categories/update', [App\Http\Controllers\Admin\CategoryController::class, 'updateSubCategory'])->name('subCategories.edit');
    Route::get('/sub-categories/remove/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'removeSubCategory'])->name('subCategories.remove');
    Route::post('/category/main-categories', [App\Http\Controllers\Admin\CategoryController::class, 'getMainCategories'])->name('categories.main.get');
    Route::post('/category/sub-categories', [App\Http\Controllers\Admin\CategoryController::class, 'getSubCategories'])->name('categories.sub.get');
    Route::get('/category/new', [App\Http\Controllers\Admin\CategoryController::class, 'newCategoryUI'])->name('categories.new');
    Route::get('/category/sub-categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'getSubCategoriesForCategory'])->name('categories.subCategory.get');
    Route::get('/category/child-categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'getChildCategoriesForSubCategory'])->name('categories.childCategory.get');

    //tags management
    Route::get('/tags', [App\Http\Controllers\Admin\TagController::class, 'index'])->name('tags.all');
    Route::post('/tags', [App\Http\Controllers\Admin\TagController::class, 'store'])->name('tags.create');
    Route::post('/tags/update', [App\Http\Controllers\Admin\TagController::class, 'update'])->name('tags.edit');
    Route::get('/tags/remove/{id}', [App\Http\Controllers\Admin\TagController::class, 'deleteTag'])->name('tags.delete');

    //posts management
    Route::get('/posts', [App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.all');
    Route::post('/posts', [App\Http\Controllers\Admin\PostController::class, 'store'])->name('posts.create');
    Route::get('/posts/new', [App\Http\Controllers\Admin\PostController::class, 'newPostUI'])->name('posts.new');
    Route::get('/posts/update/{id}', [App\Http\Controllers\Admin\PostController::class, 'editPostUI'])->name('posts.edit');
    Route::post('/posts/update', [App\Http\Controllers\Admin\PostController::class, 'update'])->name('posts.update');
    Route::get('/posts/change-status/{id}', [App\Http\Controllers\Admin\PostController::class, 'changeStatus'])->name('posts.status');
    Route::get('/posts/approve/{id}', [App\Http\Controllers\Admin\PostController::class, 'approvePost'])->name('posts.approve');
    Route::get('/posts/delete/{id}', [App\Http\Controllers\Admin\PostController::class, 'deletePost'])->name('posts.delete');
    Route::get('/posts/pending-approval', [App\Http\Controllers\Admin\PostController::class, 'postsToApproveUI'])->name('posts.approval');

    //comments management
    Route::get('/posts/comments/{id}', [App\Http\Controllers\Admin\CommentController::class, 'commentsForPost'])->name('posts.comments');
    Route::post('/posts/comments/reply', [App\Http\Controllers\Admin\CommentController::class, 'replyForComment'])->name('comments.reply');
    Route::post('/posts/add-comments',[App\Http\Controllers\Admin\CommentController::class, 'addPostComment'])->name('comments.add');
    Route::post('/posts/add-comments',[App\Http\Controllers\Admin\CommentController::class, 'addPostComment'])->name('comments.add');
    Route::get('/comments/all',[App\Http\Controllers\Admin\CommentController::class, 'allPostComments'])->name('postComments.all');
    Route::get('/comments/approve/{id}',[App\Http\Controllers\Admin\CommentController::class, 'approveComment'])->name('comments.approve');
    Route::get('/comments/delete/{id}',[App\Http\Controllers\Admin\CommentController::class, 'deleteComment'])->name('comments.delete');
    Route::get('/comments/status/{id}',[App\Http\Controllers\Admin\CommentController::class, 'changeCommentStatus'])->name('comments.status');
    Route::post('/comments/update',[App\Http\Controllers\Admin\CommentController::class, 'editComment'])->name('comments.update');

    // pages management
    Route::get('/pages',[App\Http\Controllers\Admin\PageController::class, 'index'])->name('webpages.all');
    Route::get('/pages/create',[App\Http\Controllers\Admin\PageController::class, 'createPageUI'])->name('webpages.new');
    Route::post('/pages/create',[App\Http\Controllers\Admin\PageController::class, 'store'])->name('webpages.create');
    Route::get('/pages/update/{id}',[App\Http\Controllers\Admin\PageController::class, 'editPageUI'])->name('webpages.view');
    Route::post('/pages/update',[App\Http\Controllers\Admin\PageController::class, 'update'])->name('webpages.update');
    Route::get('/pages/visible/{id}',[App\Http\Controllers\Admin\PageController::class, 'changePageVisibility'])->name('webpages.visible');
    Route::get('/pages/delete/{id}',[App\Http\Controllers\Admin\PageController::class, 'deletePage'])->name('webpages.delete');
    Route::post('/pages/sort',[App\Http\Controllers\Admin\PageController::class, 'sortPages'])->name('webpages.sort');

    //settings management
    Route::get('/settings/slider',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'uploadSliderImagesUI'])->name('settings.header');
    Route::get('/settings/banner',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'uploadBannerUI'])->name('settings.banner');
    Route::post('/settings/slider',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'uploadSliderImages'])->name('settings.headerCreate');
    Route::post('/settings/banner',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateBannerImage'])->name('settings.updateBanner');
    Route::post('/settings/header-update',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateSliderImages'])->name('settings.headerUpdate');
    Route::get('/settings/slider-delete/{id}',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'removeSliderImages'])->name('settings.sliderDelete');
    Route::get('/settings/analytics',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateAnalyticsUI'])->name('settings.analytics');
    Route::post('/settings/analytics',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateAnalytics'])->name('settings.analyticsUpdate');
    Route::get('/settings/site-settings',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'siteSettingsUI'])->name('settings.siteSettings');
    Route::post('/settings/site-settings',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateSiteSettings'])->name('settings.siteSettingsUpdate');
    Route::post('/settings/site-params',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateSiteParameters'])->name('settings.updateSiteParams');
    Route::get('/settings/site-settings',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'siteSettingsUI'])->name('settings.siteSettings');
    Route::post('/settings/site-settings/get-template',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'getTemplateForTemplateNumber'])->name('settings.getTemplateImg');
    Route::get('/settings/site-settings/active',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'getAllActiveTemplates'])->name('settings.activeTemplates');
    Route::get('/settings/countries',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'countrySettings'])->name('settings.countrySettings');
    Route::post('/settings/add-country', [App\Http\Controllers\Admin\GeneralSettingsController::class, 'addCountry'])->name('country.create');
    Route::post('/settings/check-country', [App\Http\Controllers\Admin\GeneralSettingsController::class, 'checkCountryName'])->name('country.checkName');
    Route::post('/settings/update-country',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateCountry'])->name('country.updateCountry');
    Route::post('/settings/remove-country',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'removeCountry'])->name('country.removeCountry');
    Route::post('/settings/check-name', [App\Http\Controllers\Admin\GeneralSettingsController::class, 'checkCountryName'])->name('country.checkName');
    Route::get('/settings/templates',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'getAllTemplates'])->name('settings.templates');
    Route::post('/settings/add-template',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'addNewTemplate'])->name('settings.addTemplate');
    Route::post('/settings/update-template',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateTemplate'])->name('settings.updateTemplate');
    Route::post('/settings/remove-template',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'removeTemplate'])->name('settings.removeTemplate');
    Route::get('/settings/email-settings',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'emailSettings'])->name('settings.emailSettings');
    Route::post('/settings/remove-email',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'removeEmailConfig'])->name('settings.removeEmail');
    Route::post('/settings/add-email',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'addEmailConfig'])->name('settings.addEmailConfig');
    Route::post('/settings/update-email',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateEmailConfig'])->name('settings.updateEmailConfig');
    Route::post('/settings/update-social',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateSocialLinks'])->name('settings.updateSocialLinks');
    Route::post('/settings/update-flat-rate',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateFlatRate'])->name('settings.updateFlatRate');
    Route::post('/settings/robots',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateSiteRobotsText'])->name('settings.robotsTextUpdate');
    Route::post('/settings/sitemap',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateSiteMap'])->name('settings.siteMapUpdate');
    Route::get('/settings/download/{file}',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'downloadFile'])->name('settings.download');
    Route::get('/settings/units',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'getUnitsPage'])->name('settings.units');
    Route::post('/settings/add-new-units',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'addUnits'])->name('settings.addUnits');
    Route::post('/settings/edit-unit',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateUnit'])->name('settings.updateUnit');
    Route::post('/settings/remove-unit',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'removeUnit'])->name('settings.removeUnit');
    Route::get('/settings/sidebar',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'sidebarSettingsUI'])->name('settings.sidebar');
    Route::post('/settings/sidebar',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'sidebarSettings'])->name('settings.sidebar.update');
    Route::post('/settings/configuration',[App\Http\Controllers\Admin\GeneralSettingsController::class, 'updateConfiguration'])->name('settings.configuration.update');

    //logs management
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->middleware(['auth']);

    // product management
    Route::get('/product', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.all');
    Route::get('/product/add-new', [App\Http\Controllers\Admin\ProductController::class, 'newProductUI'])->name('products.newProduct');
    Route::get('/product/edit/{id}', [App\Http\Controllers\Admin\ProductController::class, 'editProductUI'])->name('products.editProduct');
    Route::post('/product', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('product.create');
    Route::post('/product/update', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('product.edit');
    Route::get('/product/remove/{id}', [App\Http\Controllers\Admin\ProductController::class, 'remove'])->name('product.remove');
    Route::get('/product/get/{id}', [App\Http\Controllers\Admin\ProductController::class, 'getProductForId'])->name('product.get');
    Route::post('/product/related/add', [App\Http\Controllers\Admin\ProductController::class, 'addRelatedProducts'])->name('product.related.add');
    Route::get('/product/status-change/{id}', [App\Http\Controllers\Admin\ProductController::class, 'activateDeactivateProduct'])->name('product.status.change');
    Route::post('/product/check-name', [App\Http\Controllers\Admin\ProductController::class, 'checkproductName'])->name('product.checkName');
    Route::get('/product/reviews/{id}', [App\Http\Controllers\Admin\ProductController::class, 'getReviewsForProduct'])->name('products.reviews.all');
    Route::get('/product/reviews/status-change/{id}', [App\Http\Controllers\Admin\ProductController::class, 'reviewStatusChange'])->name('products.reviews.change.status');
    Route::post('/product-image/delete', [App\Http\Controllers\Admin\ProductController::class, 'deleteProductImage'])->name('delete.product.image');

    // product variants management
    Route::get('/product/variants', [App\Http\Controllers\Admin\VariantController::class, 'index'])->name('products.variants.all');
    Route::post('/product/variants/create', [App\Http\Controllers\Admin\VariantController::class, 'store'])->name('products.variants.create');
    Route::post('/product/variants/update', [App\Http\Controllers\Admin\VariantController::class, 'update'])->name('products.variants.update');
    Route::get('/product/variants/change-status/{id}', [App\Http\Controllers\Admin\VariantController::class, 'changeVariantStatus'])->name('products.variants.status.change');
    // Brand management
    Route::get('/brands/all_brands',[App\Http\Controllers\Admin\ProductController::class, 'getbrand'])->name('brands.all_brands');
    Route::post('/brands/add_brand',[App\Http\Controllers\Admin\ProductController::class, 'addbrand'])->name('brands.addBrands');
    Route::post('/brands/edit_brand',[App\Http\Controllers\Admin\ProductController::class, 'updateBrand'])->name('Brand.updateBrands');
    Route::post('/brands/remove_brand',[App\Http\Controllers\Admin\ProductController::class, 'removeBrand'])->name('Brand.removeBrand');
    Route::post('/brands/remove_image',[App\Http\Controllers\Admin\ProductController::class, 'removeBrandImages'])->name('Brand.removeImage');

    // Inventory management
    Route::get('/inventory',[App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.all');
    Route::post('/inventory/update',[App\Http\Controllers\Admin\InventoryController::class, 'update'])->name('inventory.update');
    Route::get('/product/history',[App\Http\Controllers\Admin\InventoryController::class, 'showProductInventoryHistory'])->name('inventory.history.product');
    Route::get('/product/history/{id}',[App\Http\Controllers\Admin\InventoryController::class, 'downloadProductInventoryHistory'])->name('inventory.product.history.download');

    // Zone management
    Route::get('/zones/all_zones',[App\Http\Controllers\Admin\ZoneController::class, 'getzone'])->name('zones.all_zones');
    Route::post('/zones/add-zone',[App\Http\Controllers\Admin\ZoneController::class, 'addZone'])->name('zones.addZone');
    Route::post('/zones/edit-zone',[App\Http\Controllers\Admin\ZoneController::class, 'updateZone'])->name('zones.updateZone');
    Route::post('/zones/remove-zone',[App\Http\Controllers\Admin\ZoneController::class, 'removeZone'])->name('zones.removeZone');

    // Coupon management
    Route::get('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupon.all');
    Route::post('/add-coupon', [App\Http\Controllers\Admin\CouponController::class, 'addcoupon'])->name('coupon.create');
    Route::post('/edit-coupon',[App\Http\Controllers\Admin\CouponController::class, 'updateCoupon'])->name('coupon.updateCoupon');
    Route::post('/remove-coupon',[App\Http\Controllers\Admin\CouponController::class, 'removeCoupon'])->name('coupon.removeCoupon');
    Route::post('/coupon/check-name', [App\Http\Controllers\Admin\CouponController::class, 'checkCouponName'])->name('coupon.checkName');

    // Order management
    Route::get('/orders/edit/{id}',[App\Http\Controllers\Admin\OrderController::class, 'editOrder'])->name('orders.edit');
    Route::get('/orders/change-status',[App\Http\Controllers\Admin\OrderController::class, 'changeOrderStatusUI'])->name('orders.status.change.ui');
    Route::post('/orders/change-status',[App\Http\Controllers\Admin\OrderController::class, 'changeOrderStatus'])->name('orders.status.change');
    Route::get('/orders',[App\Http\Controllers\Admin\OrderController::class, 'index']) ->name('orders.all');
    Route::get('/orders/item-delete/{id}',[App\Http\Controllers\Admin\OrderController::class, 'removeOrderItem']) ->name('orders.items.delete');
    Route::post('/orders/item/update-quantity',[App\Http\Controllers\Admin\OrderController::class, 'editOrderItemQuantity']) ->name('orders.items.updateQuantity');
    Route::post('/orders/approve',[App\Http\Controllers\Admin\OrderController::class, 'approveOrder'])->name('orders.approve');
    Route::post('/orders/initiate-cancellation',[App\Http\Controllers\Admin\OrderController::class, 'initiateOrderCancellation'])->name('orders.initiateCancellation');
    Route::get('/orders/pending-cancellations',[App\Http\Controllers\Admin\OrderController::class, 'cancellationApprovals'])->name('orders.cancellationApproval');
    Route::get('/orders/approve-cancellations/{id}',[App\Http\Controllers\Admin\OrderController::class, 'approveOrderCancellation'])->name('orders.cancel.approve');
    Route::get('/orders/cancelled',[App\Http\Controllers\Admin\OrderController::class, 'cancelledOrders'])->name('orders.cancelledOrders');
    Route::post('/orders/edit/address',[App\Http\Controllers\Admin\OrderController::class, 'updateBillingShippingAddresses']) ->name('orders.edit.addresses');
    Route::post('/orders/edit/customer',[App\Http\Controllers\Admin\OrderController::class, 'updateOrderCustomer']) ->name('orders.edit.customer');

    Route::get('/orders/statuses',[App\Http\Controllers\Admin\OrderController::class, 'showOrderStatuses'])->name('orders.statuses');
    Route::post('/orders/statuses',[App\Http\Controllers\Admin\OrderController::class, 'createOrderStatus'])->name('order.status.create');
    Route::post('/orders/status/edit',[App\Http\Controllers\Admin\OrderController::class, 'updateOrderStatus'])->name('order.status.update');
    Route::get('/orders/status/remove/{id}',[App\Http\Controllers\Admin\OrderController::class, 'removeOrderStatus'])->name('order.status.remove');
    Route::post('/orders/status/change',[App\Http\Controllers\Admin\OrderController::class, 'changeOrderStatus'])->name('order.status.change');
    Route::get('/download-order/{id}',[App\Http\Controllers\Admin\OrderController::class, 'downloadOrderInvoice'])->name('order.download');
    Route::get('/orders/not-reserved',[App\Http\Controllers\Admin\OrderController::class, 'inventoryNotReservedOrders'])->name('order.notReserved');
    Route::get('/orders/reserve-manually/{id}',[App\Http\Controllers\Admin\OrderController::class, 'reserveInventoryManually'])->name('order.reserve.manually');
    Route::get('/orders/invoices-packing-slips',[App\Http\Controllers\Admin\OrderController::class, 'invoicesAndPackingSlips'])->name('orders.invoices');
    Route::get('/orders/packing-slip/{id}',[App\Http\Controllers\Admin\OrderController::class, 'downloadPackingSlip'])->name('orders.packingSlip.download');
    Route::get('/quotations/all',[App\Http\Controllers\Admin\OrderController::class, 'viewQuotations'])->name('orders.quotations.all');
    Route::get('/quotations/download/{id}',[App\Http\Controllers\Admin\OrderController::class, 'downloadQuotation'])->name('orders.quotations.download');
    Route::get('/sales-report',[App\Http\Controllers\Admin\OrderController::class, 'salesOrderCharts'])->name('orders.sales.report');


    // inquiry management
    Route::get('/inquiries',[App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('inquiries.all');
    Route::post('/inquiries/reply',[App\Http\Controllers\Admin\InquiryController::class, 'replyInquiry'])->name('inquiries.reply');
    Route::get('/subscribers/list', [App\Http\Controllers\Admin\InquiryController::class, 'subscribersList'])->name('subscribers.list');

    // Report 
    Route::get('/report/order',[App\Http\Controllers\Admin\ReportController::class, 'orderReport'])->name('report.ordernew');

    // Advertisements management
    Route::get('/advertisements',[App\Http\Controllers\Admin\AdvertisementController::class, 'index'])->name('advertisements.all');
    Route::post('/advertisements',[App\Http\Controllers\Admin\AdvertisementController::class, 'addAdvertisement'])->name('advertisements.create');
    Route::post('/advertisements/edit',[App\Http\Controllers\Admin\AdvertisementController::class, 'editAdvertisement'])->name('advertisements.edit');
    Route::get('/advertisements/remove/{id}',[App\Http\Controllers\Admin\AdvertisementController::class, 'removeAdvertisement'])->name('advertisements.remove');
    Route::get('/advertisements/status/change/{id}',[App\Http\Controllers\Admin\AdvertisementController::class, 'changeAdvertisementStatus'])->name('advertisements.change.status');

    // Promotions management
    Route::get('/promotions',[App\Http\Controllers\Admin\PromotionController::class, 'index'])->name('promotions.all');
    Route::post('/promotions',[App\Http\Controllers\Admin\PromotionController::class, 'createPromotion'])->name('promotions.create');
    Route::post('/promotions/edit',[App\Http\Controllers\Admin\PromotionController::class, 'editPromotion'])->name('promotions.edit');
    Route::get('/promotions/remove/{id}',[App\Http\Controllers\Admin\PromotionController::class, 'removePromotion'])->name('promotions.remove');
    Route::get('/promotions/status/change/{id}',[App\Http\Controllers\Admin\PromotionController::class, 'changePromotionStatus'])->name('promotions.change.status');
    Route::get('/promotions/assign',[App\Http\Controllers\Admin\PromotionController::class, 'assignPromotionForProductUI'])->name('promotions.assignUI');
    Route::post('/promotions/assign',[App\Http\Controllers\Admin\PromotionController::class, 'assignPromotionForProduct'])->name('promotions.assign');
    Route::get('/promotions/remove-assigned/{id}',[App\Http\Controllers\Admin\PromotionController::class, 'removePromotionForProduct'])->name('promotions.assigned.remove');

    // alerts
    Route::get('/low-stock-alert',[App\Http\Controllers\Admin\InventoryController::class, 'sendLowStockAlerts'])->name('alerts.lowstock.send');
    Route::get('/deactivate-expired-promotions',[App\Http\Controllers\Admin\PromotionController::class, 'deactivateExpiredPromotions'])->name('promotions.expired.deactivate');
    
    // Export subscribers - csv export
    Route::get('export/csv',[App\Http\Controllers\Admin\SubscriberController::class, 'exportSubsCSVFile'] )->name('export.subscribers.csv');

    // errors
    Route::get('/not_allowed', function () {
        return view('admin.errors.not_allowed');
    });
    
    // Forgot password email
    Route::get('/email', function () {
        return new ForgotPasswordMail();
    });

});
