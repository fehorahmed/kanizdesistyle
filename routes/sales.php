<?php

use App\Http\Controllers\PreOrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use Illuminate\Support\Facades\Route;

 
// Sales Order
Route::get('pos', [SaleController::class, 'pos'])->name('pos')->middleware('permission:sales_order');
Route::post('pos', [SaleController::class, 'posPost'])->middleware('permission:sales_order');
Route::get('pos/edit/{order}', [SaleController::class, 'posEdit'])->name('sales_order.edit_pos')->middleware('permission:sales_receipt_offline_edit');
Route::get('pos_product_search', [SaleController::class, 'posProductSearch'])->name('pos_product_search');
Route::get('pos_preorder_product_search', [SaleController::class, 'posPreorderProductSearch'])->name('pos_preorder_product_search');
Route::get('search-barcode-autocomplete', [SaleController::class,'searchBarcodeAutocomplete'])->name('search-barcode-autocomplete');
Route::get('search-preorder-barcode-autocomplete', [SaleController::class,'searchPreorderBarcodeAutocomplete'])->name('search-preorder-barcode-autocomplete');
Route::get('party-json', [SaleController::class,'partyJson'])->name('party_json');

Route::get('pre-order', [PreOrderController::class, 'preOrder'])->name('pre_order.create');
Route::post('pre-order', [PreOrderController::class, 'preOrderPost']);
Route::get('pre-order/receipt', [PreOrderController::class, 'preOrderReceipt'])->name('pre_order.receipt');
Route::get('pre-order/receipt-datatable', [PreOrderController::class, 'preOrderReceiptDatatable'])->name('pre_order.datatable');
Route::get('pre-order/confirm/{order}', [PreOrderController::class, 'preOrderConfirm'])->name('pre_order.confirm');
Route::post('pre-order/confirm/{order}', [PreOrderController::class, 'preOrderConfirmPost']);
Route::get('pre-order/receipt/details/{order}', [PreOrderController::class, 'preOrderReceiptDetails'])->name('pre_order.receipt.details');

Route::get('sales-order', [SaleController::class, 'salesOrder'])->name('sales_order.create')->middleware('permission:sales_order');
Route::post('sales-order', [SaleController::class, 'salesOrderPost'])->middleware('permission:sales_order');
Route::get('sales-order/edit/{order}', [SaleController::class, 'salesOrderEdit'])->name('sales_order.edit')->middleware('permission:sales_receipt_online_edit');
Route::post('sales-order/edit/{order}', [SaleController::class, 'salesOrderEditPost'])->middleware('permission:sales_receipt_online_edit');
Route::get('sale-logs/{orderId}', [SaleController::class, 'saleLogs'])->name('sale_logs');

Route::get('get_customer_sales_order', [SaleController::class, 'getCustomerSalesOrder'])->name('get_customer_sales_order');
Route::get('get_sales_order_items', [SaleController::class, 'getSalesOrderItem'])->name('get_sales_order_items');

Route::get('homeo-sales-order', [SaleController::class, 'homeoSalesOrder'])->name('homeo_sales_order.create');
Route::post('homeo-sales-order', [SaleController::class, 'homeoSalesOrderPost']);
Route::get('sale-order/product/details', [SaleController::class, 'saleProductDetails'])->name('sale_product.details');

// Sales Return
Route::get('sales-return/receipt/online', [SaleReturnController::class, 'salesReturnFMReceiptOnline'])->name('sales_return_receipt_online.all')->middleware('permission:sales_return_online_receipt');
Route::get('sales-return/receipt-online-datatable', [SaleReturnController::class, 'salesReturnFMReceiptOnlineDatatable'])->name('sales_return_receipt_online.datatable');

Route::get('sales-return/receipt/offline', [SaleReturnController::class, 'salesReturnFMReceiptOffline'])->name('sales_return_receipt_offline.all')->middleware('permission:sales_return_offline_receipt');
Route::get('sales-return/receipt-offline-datatable', [SaleReturnController::class, 'salesReturnFMReceiptOfflineDatatable'])->name('sales_return_receipt_offline.datatable');

Route::get('sales-return', action: [SaleReturnController::class, 'salesReturnFMOnline'])->name('sales_return_online.create')->middleware('permission:sales_return_order_create');
Route::post('sales-return', [SaleReturnController::class, 'salesReturnFMOnlinePost']);

// Sale Receipt
Route::get('sale-receipt-offline', [SaleController::class, 'saleReceipt'])->name('sale_receipt_offline.all');
Route::get('sale-receipt-online', [SaleController::class, 'saleReceipt'])->name('sale_receipt_online.all');
Route::get('sale-receipt/details/{order}', [SaleController::class, 'saleReceiptDetails'])->name('sale_receipt.details');
Route::get('sale-receipt/print/{order}', [SaleController::class, 'saleReceiptPrint'])->name('sale_receipt.print');
Route::get('sales-pos-print/{saleOrder}', [SaleController::class, 'posPrint'])->name('pos_print');
Route::get('sale-receipt/datatable', [SaleController::class, 'saleReceiptDatatable'])->name('sale_receipt.datatable');
Route::post('sale-receipt/payment', [SaleController::class, 'makePayment'])->name('sale_receipt.make_payment');
Route::post('sales_order_payment/edit', [SaleController::class, 'salesPaymentEdit'])->name('sales_order_payment.edit');
Route::post('sale-receipt/status', [SaleController::class, 'statusChange'])->name('sale_receipt.status_change');
Route::get('sale-receipt/payment/details/{payment}', [SaleController::class, 'salePaymentDetails'])->name('sale_receipt.payment_details');
Route::get('sale-receipt/payment/print/{payment}', [SaleController::class, 'salePaymentPrint'])->name('sale_receipt.payment_print');
Route::get('sale-receipt/edit/{order}', [SaleController::class, 'saleReceip-tEdit'])->name('sale_receipt.edit');
Route::post('sale-receipt/edit/{order}', [SaleController::class, 'saleReceiptEditPost']);
Route::post('sale-receipt/delete', [SaleController::class, 'orderDelete'])->name('order_receipt_delete');
Route::get('get_product_inventory_info', [SaleController::class, 'productInventoryInfo'])->name('get_product_inventory_info');
Route::get('get_product_and_inventory_info', [SaleController::class, 'productAndInventoryInfo'])->name('get_product_and_inventory_info');

// Customer Payment
Route::get('customer-payment', [SaleController::class, 'customerPayment'])->name('customer_payment.all')->middleware('permission:customer_payment');
Route::get('customer-payment/datatable', [SaleController::class, 'customerPaymentDatatable'])->name('customer_payment.datatable')->middleware('permission:customer_payment');
Route::get('customer-payment/get-orders', [SaleController::class, 'customerPaymentGetOrders'])->name('customer_payment.get_orders')->middleware('permission:customer_payment');
Route::get('customer-payment/get-refund-orders', [SaleController::class, 'customerPaymentGetRefundOrders'])->name('customer_payment.get_refund_orders')->middleware('permission:customer_payment');
Route::post('customer-payment/payment', [SaleController::class, 'customerMakePayment'])->name('customer_payment.make_payment')->middleware('permission:customer_payment');
Route::post('customer-payment/refund', [SaleController::class, 'customerMakeRefund'])->name('customer_payment.make_refund')->middleware('permission:customer_payment');
Route::get('customer-payments/{customer}', [SaleController::class, 'customerPayments'])->name('customer_payments')->middleware('permission:customer_payment');