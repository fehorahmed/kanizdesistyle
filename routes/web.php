<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BkashController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MobileBankingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Promotion\ProductTemplateController;
use App\Http\Controllers\Promotion\PromotionTemplateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SisterConcernController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VatController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::middleware('checkDatabaseConnection')->group(function () {
    // Route::get('/', function () {
    //     return redirect()->route('login');
    // });
    Route::post('admin.ckeditor.upload', [CommonController::class, 'ckeditorUpload'])->name('admin.ckeditor.upload');
    
    Route::prefix('admin')->middleware(['auth', 'checkUserStatus','roleCheck:1,2,3'])->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //Sms Panel
        Route::get('sms', [SmsController::class, 'index'])->name('sms_panel');
        Route::post('sms', [SmsController::class, 'addPost']);

        //User
        // Route::resource('user', UserController::class)->middleware('permission:users');
        // Route::get('user-datatable', [UserController::class, 'dataTable'])->name('user.datatable');
        Route::resource('user', UserController::class);
        Route::get('user-datatable', [UserController::class, 'dataTable'])->name('user.datatable');
        Route::get('user-toggle-status/{user}', [UserController::class, 'toggleStatus'])->name('user.toggle_status');

        //User Permission
        Route::get('user-permission/{user}', [UserController::class, 'permission'])->name('user.permission');
        Route::post('user-permission/{user}', [UserController::class, 'permissionUpdate'])->name('user.permission.update');

        //Profile
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('dark-mode-change', [ProfileController::class, 'darkModeChange'])->name('dark_mode_change');
        Route::post('profile-edit', [ProfileController::class, 'editPost'])->name('profile.edit_post');
        Route::get('password-change', [ProfileController::class, 'passwordEdit'])->name('profile.password_change');
        Route::post('password-change', [ProfileController::class, 'passwordUpdate']);

        // department Branch
        Route::get('sister-concern', [SisterConcernController::class, 'index'])->name('sister_concern')->middleware('permission:sister_concern');
        Route::get('sister-concern-datatable', [SisterConcernController::class, 'dataTable'])->name('sister_concern.datatable')->middleware('permission:sister_concern');
        Route::get('sister-concern/add', [SisterConcernController::class, 'add'])->name('sister_concern.add')->middleware('permission:sister_concern_add');
        Route::post('sister-concern/add', [SisterConcernController::class, 'addPost'])->middleware('permission:sister_concern_add');
        Route::get('sister-concern/edit/{sister_concern}', [SisterConcernController::class, 'edit'])->name('sister_concern.edit')->middleware('permission:sister_concern_edit');
        Route::post('sister-concern/edit/{sister_concern}', [SisterConcernController::class, 'editPost'])->middleware('permission:sister_concern_edit');

        // order
        Route::get('order', [OrderController::class, 'index'])->name('order.index');
        Route::get('order-datatable', [OrderController::class, 'dataTable'])->name('order.datatable');
        Route::get('order/edit/{order}', [OrderController::class, 'edit'])->name('order.edit');
        Route::get('order/show/{order}', [OrderController::class, 'show'])->name('order.show');
        Route::get('frontend/order-details/{order}', [OrderController::class, 'orderDetails'])->name('order.details');

        // order items
        // Route::get('order-item-datatable', [OrderItemController::class, 'dataTable'])->name('order-items.datatable');

        // Bank
        Route::get('bank', [BankController::class, 'index'])->name('bank')->middleware('permission:bank');
        Route::get('bank-datatable', [BankController::class, 'dataTable'])->name('bank.datatable')->middleware('permission:bank');
        Route::get('bank/add', [BankController::class, 'add'])->name('bank.add')->middleware('permission:bank_add');
        Route::post('bank/add', [BankController::class, 'addPost'])->middleware('permission:bank_add');
        Route::get('bank/edit/{bank}', [BankController::class, 'edit'])->name('bank.edit')->middleware('permission:bank_edit');
        Route::post('bank/edit/{bank}', [BankController::class, 'editPost'])->middleware('permission:bank_edit');

        // Bank Branch
        Route::get('bank-branch', [BranchController::class, 'index'])->name('branch')->middleware('permission:branch');
        Route::get('branch-datatable', [BranchController::class, 'dataTable'])->name('branch.datatable')->middleware('permission:branch');
        Route::get('bank-branch/add', [BranchController::class, 'add'])->name('branch.add')->middleware('permission:branch_add');
        Route::post('bank-branch/add', [BranchController::class, 'addPost'])->middleware('permission:branch_add');
        Route::get('bank-branch/edit/{branch}', [BranchController::class, 'edit'])->name('branch.edit')->middleware('permission:branch_edit');
        Route::post('bank-branch/edit/{branch}', [BranchController::class, 'editPost'])->middleware('permission:branch_edit');

        // Bank Account
        Route::get('bank-account', [BankAccountController::class, 'index'])->name('bank_account')->middleware('permission:bank_account');
        Route::get('bank-account-datatable', [BankAccountController::class, 'dataTable'])->name('bank_account.datatable')->middleware('permission:bank_account');
        Route::get('bank-account/add', [BankAccountController::class, 'add'])->name('bank_account.add')->middleware('permission:bank_account_add');
        Route::post('bank-account/add', [BankAccountController::class, 'addPost'])->middleware('permission:bank_account_add');
        Route::get('bank-account/edit/{account}', [BankAccountController::class, 'edit'])->name('bank_account.edit')->middleware('permission:bank_account_edit');
        Route::post('bank-account/edit/{account}', [BankAccountController::class, 'editPost'])->middleware('permission:bank_account_edit');
        Route::get('bank-account/get-branches', [BankAccountController::class, 'getBranches'])->name('bank_account.get_branch');

        // Mobile Banking
        Route::get('mobile-banking', [MobileBankingController::class, 'index'])->name('mobile_banking')->middleware('permission:mobile_banking');
        Route::get('mobile-banking-datatable', [MobileBankingController::class, 'dataTable'])->name('mobile_banking.datatable')->middleware('permission:mobile_banking');
        Route::get('mobile-banking/add', [MobileBankingController::class, 'add'])->name('mobile_banking.add')->middleware('permission:mobile_banking_add');
        Route::post('mobile-banking/add', [MobileBankingController::class, 'addPost'])->middleware('permission:mobile_banking_add');
        Route::get('mobile-banking/edit/{mobileBanking}', [MobileBankingController::class, 'edit'])->name('mobile_banking.edit')->middleware('permission:mobile_banking_edit');
        Route::post('mobile-banking/edit/{mobileBanking}', [MobileBankingController::class, 'editPost'])->middleware('permission:mobile_banking_edit');


        //Unit
        Route::resource('unit', UnitController::class)->middleware('permission:unit');
        Route::get('unit-datatable', [UnitController::class, 'dataTable'])->name('unit.datatable')->middleware('permission:unit');
        Route::get('unit-toggle-status/{unit}', [UnitController::class, 'toggleStatus'])->name('unit.toggle_status');
        
        //Brand
        Route::resource('brand', BrandController::class);
        Route::get('brand-datatable', [BrandController::class, 'dataTable'])->name('brand.datatable');
        Route::post('brand/instant-add', [BrandController::class, 'instantAddPost'])->name('brand.instant_add');
        Route::get('brand-toggle-status/{brand}', [BrandController::class, 'toggleStatus'])->name('brand.toggle_status');

        //Filter
        Route::resource('filters', FilterController::class);
        Route::get('filters-datatable', [FilterController::class, 'dataTable'])->name('filters.datatable');

        //category
        Route::resource('category', CategoryController::class)->middleware('permission:category');
        Route::get('category-datatable', [CategoryController::class, 'dataTable'])->name('category.datatable')->middleware('permission:category');
        Route::get('category/add-edit-filter/{category}', [CategoryController::class, 'addEditFilter'])->name('category.add_edit_filter')->middleware('permission:category');
        Route::post('category/add-edit-filter/{category}', [CategoryController::class, 'addEditFilterPost'])->middleware('permission:category');
        Route::get('category-toggle-status/{category}', [CategoryController::class, 'toggleStatus'])->name('category.toggle_status');
        
        //Sub-Category
        Route::resource('sub-category', SubCategoryController::class)->middleware('permission:sub_category');
        Route::get('sub-category-datatable', [SubCategoryController::class, 'dataTable'])->name('sub-category.datatable')->middleware('permission:sub_category');
        Route::get('sub-category-toggle-status/{subCategory}', [SubCategoryController::class, 'toggleStatus'])->name('sub-category.toggle_status');

        // Supplier
        Route::get('supplier', [SupplierController::class, 'index'])->name('supplier')->middleware('permission:supplier');
        Route::get('supplier-datatable', [SupplierController::class, 'dataTable'])->name('supplier.datatable')->middleware('permission:supplier');
        Route::get('supplier/add', [SupplierController::class, 'add'])->name('supplier.add')->middleware('permission:supplier_add');
        Route::post('supplier/add', [SupplierController::class, 'addPost'])->middleware('permission:supplier_add');
        Route::post('supplier/instant-add', [SupplierController::class, 'addInstantPost'])->name('supplier.instant_add')->middleware('permission:supplier');
        Route::get('supplier/edit/{supplier}', [SupplierController::class, 'edit'])->name('supplier.edit')->middleware('permission:supplier_edit');
        Route::post('supplier/edit/{supplier}', [SupplierController::class, 'editPost'])->middleware('permission:supplier_edit');
        Route::get('supplier-toggle-status/{supplier}', [SupplierController::class, 'toggleStatus'])->name('supplier.toggle_status');

        //Product
        Route::get('product', [ProductController::class, 'index'])->name('product')->middleware('permission:product');
        Route::get('product-datatable', [ProductController::class, 'dataTable'])->name('product.datatable')->middleware('permission:product');
        Route::get('product/add', [ProductController::class, 'add'])->name('product.add')->middleware('permission:product_edit');
        Route::post('product/add', [ProductController::class, 'addPost'])->middleware('permission:product_edit');
        Route::post('product/instant-add', [ProductController::class, 'addInstantPost'])->name('product.instant_add')->middleware('permission:product');
        Route::get('product/edit/{product}', [ProductController::class, 'edit'])->name('product.edit')->middleware('permission:product_edit');
        Route::post('product/edit/{product}', [ProductController::class, 'editPost'])->middleware('permission:product_edit');
        Route::get('product-toggle-status/{product}', [ProductController::class, 'toggleStatus'])->name('product.toggle_status');
        Route::get('product-toggle-show/{product}', [ProductController::class, 'toggleShow'])->name('product.toggle_show');
        Route::get('get_product_sub_category', [ProductController::class, 'productSubCategory'])->name('get_product_sub_category')->middleware('permission:product');
        Route::get('get_sub_category_with_product', [ProductController::class, 'getSubCategoryWithProduct'])->name('get_sub_category_with_product');
        Route::get('product/discount-settings', [ProductController::class, 'discountSettings'])->name('product.discount_settings')->middleware('permission:product');
        Route::post('product/discount-settings', [ProductController::class, 'discountSettingsPost'])->middleware('permission:product');
        Route::get('get_product_by_category', [ProductController::class, 'getProducts'])->name('get_product_by_category');
        Route::get('get-product-batches', [ProductController::class, 'getProductBatches'])->name('get_product_batches');
        
        // Customer
        Route::get('customer', [CustomerController::class, 'index'])->name('customer')->middleware('permission:customer');
        Route::get('customer/add', [CustomerController::class, 'add'])->name('customer.add')->middleware('permission:customer_add');
        Route::post('customer/add', [CustomerController::class, 'addPost'])->middleware('permission:customer_add');
        Route::get('customer/edit/{customer}', [CustomerController::class, 'edit'])->name('customer.edit')->middleware('permission:customer_edit');
        Route::post('customer/edit/{customer}', [CustomerController::class, 'editPost'])->middleware('permission:customer_edit');
        Route::get('customer/datatable', [CustomerController::class, 'datatable'])->name('customer.datatable')->middleware('permission:customer');
        Route::post('/ajax-customer', [CustomerController::class, 'addAjaxPost'])->name('customer.add_ajax_customer');
        Route::get('customer-toggle-status/{customer}', [CustomerController::class, 'toggleStatus'])->name('customer.toggle_status');

        // Product Sale Information
        Route::get('sale-information', [SaleController::class, 'saleInformation'])->name('sale_information.index')->middleware('permission:product_sale_information');
        Route::post('sale-information/post', [SaleController::class, 'saleInformationPost'])->name('sale_information.post')->middleware('permission:product_sale_information');
        Route::get('sale-information/print/{purchaseOrder}/{saleOrder}', [SaleController::class, 'saleInformationPrint'])->name('sale_information.print')->middleware('permission:product_sale_information');

        //Vat
        Route::get('vat-payment', [VatController::class,'vatPayment'])->name('vat_payment');
        Route::get('vat-register', [VatController::class,'vatRegister'])->name('vat_register');
        Route::get('vat-certificate-6.6', [VatController::class,'vatCertificateSixPointSix'])->name('vat_certificate_six_point_six');
        Route::post('vat-payout', [VatController::class,'vatPayout'])->name('vat_payout');

        // Account Head Type
        Route::get('account-head/type', [AccountsController::class, 'accountHeadType'])->name('account_head.type');
        Route::get('account-head/type/add', [AccountsController::class, 'accountHeadTypeAdd'])->name('account_head.type.add');
        Route::post('account-head/type/add', [AccountsController::class, 'accountHeadTypeAddPost']);
        Route::get('account-head/type/edit/{type}', [AccountsController::class, 'accountHeadTypeEdit'])->name('account_head.type.edit');
        Route::post('account-head/type/edit/{type}', [AccountsController::class, 'accountHeadTypeEditPost']);

        // Account Head Sub Type
        Route::get('account-head/sub-type', [AccountsController::class, 'accountHeadSubType'])->name('account_head.sub_type');
        Route::get('account-head/sub-type/add', [AccountsController::class, 'accountHeadSubTypeAdd'])->name('account_head.sub_type.add');
        Route::post('account-head/sub-type/add', [AccountsController::class, 'accountHeadSubTypeAddPost']);
        Route::get('account-head/sub-type/edit/{subType}', [AccountsController::class, 'accountHeadSubTypeEdit'])->name('account_head.sub_type.edit');
        Route::post('account-head/sub-type/edit/{subType}', [AccountsController::class, 'accountHeadSubTypeEditPost']);

        // Transaction
        Route::get('transaction', [AccountsController::class, 'transactionIndex'])->name('transaction.all');
        Route::get('transaction/datatable', [AccountsController::class, 'transactionDatatable'])->name('transaction.datatable');
        Route::get('transaction/add', [AccountsController::class, 'transactionAdd'])->name('transaction.add');
        Route::post('transaction/add', [AccountsController::class, 'transactionAddPost']);
        Route::post('transaction/edit', [AccountsController::class, 'transactionEditPost'])->name('transaction.edit_post');
        Route::get('transaction/details/json', [AccountsController::class, 'transactionDetailsJson'])->name('transaction.details_json');
        Route::get('transaction/details/{transaction}', [AccountsController::class, 'transactionDetails'])->name('transaction.details');

        // Balance Transfer
        Route::get('balance-transfer/add', [AccountsController::class, 'balanceTransferAdd'])->name('balance_transfer.add');
        Route::post('balance-transfer/add', [AccountsController::class, 'balanceTransferAddPost']);

        //Report
        Route::get('report/purchase', [ReportController::class, 'purchase'])->name('report.purchase')->middleware('permission:purchase_report');
        Route::get('report/product-wise-purchase', [ReportController::class, 'productWisePurchase'])->name('report.product_wise_purchase')->middleware('permission:purchase_report');
        Route::get('report/product-wise-summary', [ReportController::class, 'productWiseSummary'])->name('report.product_wise_summary')->middleware('permission:purchase_report');
        Route::get('report/stock-report', [ReportController::class, 'stockReport'])->name('report.stock_report')->middleware('permission:purchase_report');
        Route::get('report/stock-statement', [ReportController::class, 'stockStatement'])->name('report.stock_statement')->middleware('permission:stock_report');
        Route::get('report/sale', [ReportController::class, 'sale'])->name('report.sale')->middleware('permission:sales_report');
        Route::get('report/sale-report', [ReportController::class, 'saleReport'])->name('report.sale_report')->middleware('permission:sales_report');
        Route::get('report/payment-report', [ReportController::class, 'paymentReport'])->name('report.payment_report')->middleware('permission:payment_receive_report');
        Route::get('report/appointment-report', [ReportController::class, 'appointmentReport'])->name('report.appointment_report')->middleware('permission:appointment_report');
        Route::get('report/pathology-report', [ReportController::class, 'pathologyReport'])->name('report.pathology_report')->middleware('permission:pathology_report');
        Route::get('report/pathology-test-report', [ReportController::class, 'pathologyTestReport'])->name('report.pathology_test_report')->middleware('permission:pathology_test_count_report');
        
        Route::get('report/commission-report', [ReportController::class, 'commissionReport'])->name('report.commission_report')->middleware('permission:commission_report');
        Route::get('report/deduction-report', [ReportController::class, 'deductionReport'])->name('report.deduction_report')->middleware('permission:deduction_report');

        Route::get('report/sale/cost-price', [ReportController::class, 'saleCostPrice'])->name('report.sale.cost.price');
        Route::get('selling-product-report', [ReportController::class, 'sellingProductReport'])->name('selling_product_report')->middleware('permission:selling_product_report');

        Route::get('report/balance-sheet', [ReportController::class, 'balanceSheet'])->name('report.balance_sheet');
        Route::get('report/profit-and-loss', [ReportController::class, 'profitAndLoss'])->name('report.profit_and_loss')->middleware('permission:profit_and_loss');
        Route::get('report/ledger', [ReportController::class, 'ledger'])->name('report.ledger');
        Route::get('report/transaction', [ReportController::class, 'transaction'])->name('report.transaction');
        Route::get('report/department-wise-customer', [ReportController::class, 'departmentWiseCustomer'])->name('report.department_wise_customer');

        Route::get('report/supplier-ledger', [ReportController::class, 'supplierLedger'])->name('report.supplier_ledger');
        Route::get('report/customer-ledger', [ReportController::class, 'customerLedger'])->name('report.customer_ledger');
        Route::get('report/cashbook', [ReportController::class, 'cashbook'])->name('report.cashbook');
        Route::get('report/bankbook', [ReportController::class, 'bankbook'])->name('report.bankbook');


        //Common Controller
        Route::get('cash', [CommonController::class, 'cash'])->name('cash')->middleware('permission:cash');
        Route::post('cash', [CommonController::class, 'cashPost'])->middleware('permission:cash');
        Route::post('cash-in-out', [CommonController::class, 'CashInOutPost'])->name('cash_in_out')->middleware('permission:cash');
        Route::get('get_product_details', [CommonController::class, 'getProductDetails'])->name('get_product_details');   //used to access
        Route::get('get_product_inventory', [CommonController::class, 'getProductInventory'])->name('get_product_inventory');
        Route::get('get_product_inventory_batch_witch', [CommonController::class, 'getProductInventoryBatchWise'])->name('get_product_inventory_batch_wise');
        Route::get('get_pack_size', [CommonController::class, 'getPackSize'])->name('get_pack_size');
        Route::get('get-branch', [CommonController::class, 'getBranch'])->name('get_branch');
        Route::get('get-bank-account', [CommonController::class, 'getBankAccount'])->name('get_bank_account');
        Route::get('order-details', [CommonController::class, 'orderDetails'])->name('get_order_details');
        Route::get('acupunture-order-details', [CommonController::class, 'acupuntureOrderDetails'])->name('get_acupunture_order_details');
        Route::get('get-account-head-type', [CommonController::class, 'getAccountHeadType'])->name('get_account_head_type');
        Route::get('get-account-head-sub-type', [CommonController::class, 'getAccountHeadSubType'])->name('get_account_head_sub_type');
        Route::get('get-serial-suggestion', [CommonController::class, 'getSerialSuggestion'])->name('get_serial_suggestion');

        Route::get('get_account_head', [CommonController::class, 'getAccountHead'])->name('get_account_head');
        Route::get('get_stock_info', [CommonController::class, 'getStockInfo'])->name('get_stock_info');
        Route::get('get_stock_info', [CommonController::class, 'getStockInfo'])->name('get_stock_info');
        Route::get('get_sales_orders', [CommonController::class, 'getSalesOrders'])->name('get_sales_orders');
        Route::get('get_sales_orders_customer', [CommonController::class, 'getSalesOrdersCustomer'])->name('get_sales_orders_customer');
        Route::get('get_sales_order_details', [CommonController::class, 'getSalesOrderDetails'])->name('get_sales_order_details');
        Route::get('get_distribution_product_info', [CommonController::class, 'getDistributionProductInfo'])->name('get_distribution_product_info');
        Route::get('get_collection_amount', [CommonController::class, 'getCollectionAmount'])->name('get_collection_amount');
        Route::get('get-patient-info/{id}', [CommonController::class, 'getPatientInfo'])->name('getPatientInfo');
        Route::get('get-patient-number-suggestion', [CommonController::class, 'patientNumberSuggestion'])->name('get_patient_number_suggestion');


        //Purchase Route
        require __DIR__ . '/purchase.php';

        //Sales Route
        require __DIR__ . '/sales.php';

        //Ecommerce
        require __DIR__ . '/ecommerce.php';

        //Promotion
        require __DIR__ . '/promotion.php';
    });

    Route::get('sale-order/receipt/print/{saleOrder}', [OrderController::class, 'orderReceiptPrint'])->name('sale_order_receipt_print');
    
    // Product Promotion
    Route::get('promotion/{slug}', [ProductTemplateController::class, 'show'])->name('product-promotion.show');
    // Promotion Template (public show by slug)
    Route::get('promotion-tpl/{slug}', [PromotionTemplateController::class, 'show'])->name('promotion-template.show.slug');

    require __DIR__ . '/auth.php';
});

// Checkout (URL) User Part
Route::get('/bkash-pay', [BkashController::class, 'payment'])->name('url-pay');
Route::get('/bkash-create', [BkashController::class, 'createPayment'])->name('bkash-create');
Route::get('/bkash-callback', [BkashController::class, 'callback'])->name('url-callback');

// Checkout (URL) Admin Part
Route::get('/bkash-refund', [BkashController::class, 'getRefund'])->name('url-get-refund');
Route::post('/bkash-refund', [BkashController::class, 'refundPayment'])->name('url-post-refund');
Route::get('/bkash-search', [BkashController::class, 'getSearchTransaction'])->name('url-get-search');
Route::post('/bkash-search', [BkashController::class, 'searchTransaction'])->name('url-post-search');
Route::get('/bkash-query/{paymentID}', [BkashController::class, 'queryPaymentAPI'])->name('url-get-query');

Route::get('/email', [BkashController::class, 'sendBulkEmailUsingGuzzle'])->name('email-test');


// Route::get('my-sql-db-connection',[SettingsController::class,'mySqlDbConnection'])
//     ->name('my_sql_db_connection');
// Route::post('my-sql-db-connection',[SettingsController::class,'mySqlDbConnectionUpdate']);

Route::get('/clear', function () {
    Artisan::call('cache:forget spatie.permission.cache');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});

Route::get('/{any}', function () {
    $setting = \App\Models\Setting::select('primary_color')->first();
    return view('frontend.index', compact('setting'));
})->where('any', '.*');
