<?php

use App\Http\Controllers\Promotion\ProductTemplateController;
use App\Http\Controllers\Promotion\PromotionController;
use App\Http\Controllers\Promotion\PromotionOrderController;
use App\Http\Controllers\Promotion\PromotionTemplateController;
use App\Http\Controllers\SonologyController;
use App\Http\Controllers\SonologyTestReportController;
use Illuminate\Support\Facades\Route;

//Promotion
Route::prefix('promotion')->group(function(){
    //Product Template
    Route::resource('product-template', ProductTemplateController::class);
    Route::get('product-template-datatable', [ProductTemplateController::class, 'dataTable'])->name('product-template.datatable');
    Route::post('product-template-order-submit/{id}', [ProductTemplateController::class, 'orderSubmit'])->name('product-template.order.submit');
    Route::get('product-template-order-success/{order}', [ProductTemplateController::class, 'success'])->name('product-template.order.success');

    //Promotion Template
    Route::resource('promotion-template', PromotionTemplateController::class);
    Route::get('promotion-template-datatable', [PromotionTemplateController::class, 'dataTable'])->name('promotion-template.datatable');
    Route::post('promotion-template-order-submit/{id}', [PromotionTemplateController::class, 'orderSubmit'])->name('promotion-template.order.submit');
    Route::get('promotion-template-order-success/{order}', [PromotionTemplateController::class, 'success'])->name('promotion-template.order.success');

    // Promotion Order
    Route::get('promotion-order', [PromotionOrderController::class, 'promotionOrder'])->name('promotion_order');
    Route::get('promotion-order-datatable', [PromotionOrderController::class, 'promotionOrderDataTable'])->name('promotion_order.datatable');
    Route::get('promotion/order/confirm/{order}', [PromotionOrderController::class, 'promotionOrderConfirm'])->name('promotion_order.confirm');
    Route::post('promotion/order/confirm/{order}', [PromotionOrderController::class, 'promotionOrderConfirmPost']);
    Route::get('promotion/order/details/{order}', [PromotionOrderController::class, 'promotionOrderDetails'])->name('promotion_order.details');
    Route::post('promotion/order/status', [PromotionOrderController::class, 'promotionOrderStatus'])->name('promotion_order.status_change');

});