<?php 

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;


//Purchase Order
Route::get('purchase-order', [PurchaseOrderController::class, 'purchaseOrder'])->name('purchase_order.create')->middleware('permission:purchase_order');
Route::post('purchase-order', [PurchaseOrderController::class, 'purchaseOrderPost'])->middleware('permission:purchase_order');
Route::get('purchase-receipt/edit/{order}', [PurchaseOrderController::class, 'purchaseReceiptEdit'])->name('purchase_receipt.edit')->middleware('permission:purchase_receipt_edit');
Route::post('purchase-receipt/edit/{order}', [PurchaseOrderController::class, 'purchaseReceiptEditPost']);
Route::post('purchase-import', [PurchaseOrderController::class,'purchaseImport'])->name('purchase_import');
Route::post('purchase-delete', [PurchaseOrderController::class,'purchaseDelete'])->name('purchase_delete');
Route::get('purchase-logs/{orderId}', [PurchaseOrderController::class, 'purchaseLogs'])->name('purchase_logs');

// Purchase Receipt
Route::get('purchase-receipt', [PurchaseOrderController::class, 'purchaseReceipt'])->name('purchase_receipt.all')->middleware('permission:purchase_receipt');
Route::get('purchase-receipt/details/{order}', [PurchaseOrderController::class, 'purchaseReceiptDetails'])->name('purchase_receipt.details')->middleware('permission:purchase_receipt');
Route::get('purchase-product-json', [PurchaseOrderController::class, 'purchaseProductJson'])->name('purchase_product.json');

Route::get('purchase-receipt/print/{order}', [PurchaseOrderController::class, 'purchaseReceiptPrint'])->name('purchase_receipt.print');
Route::get('purchase-receipt/datatable', [PurchaseOrderController::class, 'purchaseReceiptDatatable'])->name('purchase_receipt.datatable');
Route::get('purchase-receipt/payment/details/{payment}', [PurchaseOrderController::class, 'purchasePaymentDetails'])->name('purchase_receipt.payment_details');
Route::get('purchase-receipt/bar-code/{order}', [PurchaseOrderController::class, 'barCode'])->name('purchase_receipt.bar_code');
Route::get('inventory/bar-code/{purchaseInventory}', [PurchaseOrderController::class, 'inventoryBarCode'])->name('inventory.bar_code');
Route::get('purchase-receipt/bar-code/print/{order}', [PurchaseOrderController::class, 'barCodePrint'])->name('purchase_receipt.bar_code_print');
Route::get('purchase-receipt/qr-code/{order}', [PurchaseOrderController::class, 'qrCode'])->name('purchase_receipt.qr_code');
Route::get('purchase-receipt/qr-code/print/{order}', [PurchaseOrderController::class, 'qrCodePrint'])->name('purchase_receipt.qr_code_print');
// Route::get('purchase-receipt/edit/{order}', [PurchaseOrderController::class, 'purchaseReceiptEdit'])->name('purchase_receipt.edit');
// Route::post('purchase-receipt/edit/{order}', [PurchaseOrderController::class, 'purchaseReceiptEditPost']);
Route::get('purchase-receipt/check-delete-status', [PurchaseOrderController::class, 'checkDeleteStatus'])->name('purchase_receipt.check_delete_status');

// Supplier Payment
Route::get('supplier-payment', [PurchaseOrderController::class, 'supplierPayment'])->name('supplier_payment.all')->middleware('permission:supplier_payment');
Route::get('supplier-payment/get-orders', [PurchaseOrderController::class, 'supplierPaymentGetOrders'])->name('supplier_payment.get_orders')->middleware('permission:supplier_payment');
Route::get('supplier-payment/get-refund-orders', [PurchaseOrderController::class, 'supplierPaymentGetRefundOrders'])->name('supplier_payment.get_refund_orders')->middleware('permission:supplier_payment');
Route::get('supplier-payment/order-details', [PurchaseOrderController::class, 'supplierPaymentOrderDetails'])->name('supplier_payment.order_details')->middleware('permission:supplier_payment');
Route::post('supplier-payment/payment', [PurchaseOrderController::class, 'makePayment'])->name('supplier_payment.make_payment')->middleware('permission:supplier_payment');
Route::post('supplier-payment/refund', [PurchaseOrderController::class, 'makeRefund'])->name('supplier_payment.make_refund')->middleware('permission:supplier_payment');
Route::get('supplier-payments/{supplier}', [PurchaseOrderController::class, 'supplierPayments'])->name('supplier_payments')->middleware('permission:supplier_payment');

// Purchase Inventory
Route::get('batch-wise-stock', [PurchaseOrderController::class, 'batchWiseInventoryStock'])->name('batch_wise_inventory.all')->middleware('permission:batch_wise_stock');
Route::get('stock', [PurchaseOrderController::class, 'inventoryStock'])->name('inventory.all')->middleware('permission:stock');
Route::get('batch-wise-stock/datatable', [PurchaseOrderController::class, 'purchaseInventoryDatatable'])->name('purchase_inventory.datatable')->middleware('permission:batch_wise_stock');
Route::get('stock/datatable', [PurchaseOrderController::class, 'inventoryStockDatatable'])->name('inventory_stock.datatable')->middleware('permission:stock');
Route::get('category-wise-stock', [ProductController::class, 'categoryWiseStock'])->name('category_wise_stock')->middleware('permission:batch_wise_stock');

Route::post('inventory-price/update', [PurchaseOrderController::class, 'updateSellingPrice'])->name('inventory_price.update');

Route::post('inventory/damage-add',[PurchaseOrderController::class,'damageAdd'])->name('damage.add');

Route::get('out-of-stock', [PurchaseOrderController::class, 'outOfStock'])->name('out_of_stock.all');
Route::get('out-of-stock/datatable', [PurchaseOrderController::class, 'outOfStockDatatable'])->name('out_of_stock.datatable');

Route::get('alert-quantity-stock', [PurchaseOrderController::class, 'alertQuantityStock'])->name('alert_quantity_stock.all');
Route::get('alert-quantity-stock/datatable', [PurchaseOrderController::class, 'alertQuantityStockDatatable'])->name('alert_quantity_stock.datatable');

Route::get('near-to-expire-stock', [PurchaseOrderController::class, 'nearToExpireStock'])->name('near_to_expire_stock.all');
Route::get('near-to-expire-stock/datatable', [PurchaseOrderController::class, 'nearToExpireStockDatatable'])->name('near_to_expire_stock.datatable');

Route::get('purchase-inventory/details/datatable', [PurchaseOrderController::class, 'purchaseInventoryDetailsDatatable'])->name('purchase_inventory.details.datatable');
Route::get('purchase-inventory/details/{product}/{sisterConcern}/{batch}', [PurchaseOrderController::class, 'purchaseInventoryDetails'])->name('purchase_inventory.details');
Route::get('purchase-inventory/bar-code/{product}', [PurchaseOrderController::class, 'purchaseInventoryBarCode'])->name('purchase_inventory.bar_code');
Route::get('purchase-inventory/qr-code/{product}', [PurchaseOrderController::class, 'purchaseInventoryQrCode'])->name('purchase_inventory.qr_code');


