<?php

use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\PageController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EcomProductController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

//Settings
Route::get('ecommerce/settings/update/{setting}', [SettingController::class, 'update'])->name('ecom_settings.update')->middleware('permission:ecom_settings');
Route::post('ecommerce/settings/update/{setting}', [SettingController::class, 'updatePost']);

// Notice
Route::get('ecommerce/notice', [NoticeController::class, 'index'])->name('ecom_notice')->middleware('permission:notices');
Route::get('ecommerce/notice-datatable', [NoticeController::class, 'dataTable'])->name('ecom_notice.datatable')->middleware('permission:notices');
Route::get('ecommerce/notice/add', [NoticeController::class, 'add'])->name('ecom_notice.add')->middleware('permission:notice_add');
Route::post('ecommerce/notice/add', [NoticeController::class, 'addPost']);
Route::get('ecommerce/notice/edit/{notice}', [NoticeController::class, 'edit'])->name('ecom_notice.edit')->middleware('permission:notice_edit');
Route::post('ecommerce/notice/edit/{notice}', [NoticeController::class, 'editPost']);

// Banner
Route::get('ecommerce/banner', [BannerController::class, 'index'])->name('ecom_banner')->middleware('permission:banners');
Route::get('ecommerce/banner-datatable', [BannerController::class, 'dataTable'])->name('ecom_banner.datatable')->middleware('permission:banners');
Route::get('ecommerce/banner/add', [BannerController::class, 'add'])->name('ecom_banner.add')->middleware('permission:banner_add');
Route::post('ecommerce/banner/add', [BannerController::class, 'addPost']);
Route::get('ecommerce/banner/edit/{banner}', [BannerController::class, 'edit'])->name('ecom_banner.edit')->middleware('permission:banner_edit');
Route::post('ecommerce/banner/edit/{banner}', [BannerController::class, 'editPost']);

// Gallery
Route::get('ecommerce/gallery', [GalleryController::class, 'index'])->name('ecom_gallery')->middleware('permission:gallery');
Route::get('ecommerce/gallery-datatable', [GalleryController::class, 'dataTable'])->name('ecom_gallery.datatable')->middleware('permission:gallery');
Route::get('ecommerce/gallery/add', [GalleryController::class, 'add'])->name('ecom_gallery.add')->middleware('permission:gallery_add');
Route::post('ecommerce/gallery/add', [GalleryController::class, 'addPost']);
Route::get('ecommerce/gallery/edit/{gallery}', [GalleryController::class, 'edit'])->name('ecom_gallery.edit')->middleware('permission:gallery_edit');
Route::post('ecommerce/gallery/edit/{gallery}', [GalleryController::class, 'editPost']);

// Page
Route::get('ecommerce/page', [PageController::class, 'index'])->name('ecom_page');
Route::get('ecommerce/page-datatable', [PageController::class, 'dataTable'])->name('ecom_page.datatable');
Route::get('ecommerce/page/add', [PageController::class, 'add'])->name('ecom_page.add');
Route::post('ecommerce/page/add', [PageController::class, 'addPost']);
Route::get('ecommerce/page/edit/{page}', [PageController::class, 'edit'])->name('ecom_page.edit');
Route::post('ecommerce/page/edit/{page}', [PageController::class, 'editPost']);

// Coupon
Route::prefix('ecommerce/coupon')->group(function () {
    Route::get('/', [CouponController::class, 'index'])->name('ecom_coupon');
    Route::get('/datatable', [CouponController::class, 'dataTable'])->name('ecom_coupon.datatable');
    Route::get('/add', [CouponController::class, 'add'])->name('ecom_coupon.add'); 
    Route::post('/add', [CouponController::class, 'addPost']); 
    Route::get('/edit/{coupon}', [CouponController::class, 'edit'])->name('ecom_coupon.edit'); 
    Route::post('/edit/{coupon}', [CouponController::class, 'editPost']); 
    Route::delete('/delete/{coupon}', [CouponController::class, 'delete'])->name('ecom_coupon.delete'); 
    Route::post('/toggle-status/{coupon}', [CouponController::class, 'toggleStatus'])->name('ecom_coupon.toggleStatus');
});

// Blog
Route::get('ecommerce/blog', [BlogController::class, 'index'])->name('ecom_blog')->middleware('permission:blogs');
Route::get('ecommerce/blog-datatable', [BlogController::class, 'dataTable'])->name('ecom_blog.datatable')->middleware('permission:blogs');
Route::get('ecommerce/blog/add', [BlogController::class, 'add'])->name('ecom_blog.add')->middleware('permission:blog_add');
Route::post('ecommerce/blog/add', [BlogController::class, 'addPost']);
Route::get('ecommerce/blog/edit/{blog}', [BlogController::class, 'edit'])->name('ecom_blog.edit')->middleware('permission:blog_edit');
Route::post('ecommerce/blog/edit/{blog}', [BlogController::class, 'editPost']);

//Product
Route::get('ecommerce/product', [EcomProductController::class, 'index'])->name('ecom_product')->middleware('permission:product');
Route::get('ecommerce/product-datatable', [EcomProductController::class, 'dataTable'])->name('ecom_product.datatable')->middleware('permission:product');
Route::get('ecommerce/product/add', [EcomProductController::class, 'add'])->name('ecom_product.add')->middleware('permission:product_add');
Route::post('ecommerce/product/add', [EcomProductController::class, 'addPost']);
Route::get('ecommerce/product/edit/{product}', [EcomProductController::class, 'edit'])->name('ecom_product.edit')->middleware('permission:product_edit');
Route::post('ecommerce/product/edit/{product}', [EcomProductController::class, 'editPost']);
Route::get('ecommerce/product-images/{product}', [EcomProductController::class, 'productImages'])->name('ecom_product.images')->middleware('permission:product_images');
Route::post('ecommerce/product-images/{product}', [EcomProductController::class, 'productImagesPost']);
Route::post('ecommerce/product-upload-images', [EcomProductController::class, 'productImagesUpload'])->name('ecom_product.upload_images');
Route::get('ecommerce/product-filters/{product}', [EcomProductController::class, 'productFilters'])->name('ecom_product.filters');
Route::post('ecommerce/product-filters/{product}', [EcomProductController::class, 'productFiltersPost']);
Route::delete('ecommerce/product-delete-image/{product}', [EcomProductController::class, 'productDeleteImage'])->name('ecom_product.delete_image');

// Order
Route::get('ecommerce/order', [OrderController::class, 'index'])->name('ecom_order')->middleware('permission:ecom_orders');
Route::get('ecommerce/order-datatable', [OrderController::class, 'dataTable'])->name('ecom_order.datatable')->middleware('permission:ecom_orders');
Route::get('ecommerce/order/confirm/{order}', [OrderController::class, 'salesConfirm'])->name('ecom_order.confirm')->middleware('permission:ecom_order_confirm');
Route::post('ecommerce/order/confirm/{order}', [OrderController::class, 'salesConfirmPost']);
Route::get('ecommerce/order/details/{order}', [OrderController::class, 'details'])->name('ecom_order.details');
Route::post('ecommerce/order/status', [OrderController::class, 'orderStatus'])->name('ecom_order.status_change')->middleware('permission:ecom_order_status');

// Contact
Route::get('ecommerce/contact', [ContactController::class, 'index'])->name('ecom_contact')->middleware('permission:contacts');
Route::get('ecommerce/contact-datatable', [ContactController::class, 'dataTable'])->name('ecom_contact.datatable')->middleware('permission:contacts');
Route::get('ecommerce/contact/add', [ContactController::class, 'add'])->name('ecom_contact.add')->middleware('permission:contact_add');
Route::post('ecommerce/contact/add', [ContactController::class, 'addPost']);
Route::get('ecommerce/contact/edit/{contact}', [ContactController::class, 'edit'])->name('ecom_contact.edit')->middleware('permission:contact_edit');
Route::post('ecommerce/contact/edit/{contact}', [ContactController::class, 'editPost']);