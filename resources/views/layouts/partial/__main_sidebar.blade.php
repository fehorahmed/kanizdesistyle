<aside
    class="main-sidebar elevation-4 sidebar-dark-info">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}"
        class="brand-link {{ auth()->user()->theme_mode == 1 ? 'bg-white' : ' bg-dark' }} ">
        <img src="{{ asset('img/logo.jpg') }}" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text"><b>Admin</b>Panel</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-child-indent nav-flat " data-widget="treeview"
                role="menu" data-accordion="false">
                {{-- @can('dashboard') --}}
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>

                    </li>
                {{-- @endcan --}}
                <?php
                $subMenu = [
                    'sister_concern', 'sister_concern.add', 'sister_concern.edit', 'unit.index', 'unit.create', 'unit.edit', 
                    'brand.index', 'brand.create', 'brand.edit', 'pack_size', 'pack_size.add', 'pack_size.edit', 
                    'category.index', 'category.create', 'category.edit', 'product', 'product.add', 'product.edit', 
                    'supplier', 'supplier.add', 'supplier.edit', 'customer', 'customer.add', 'customer.edit', 'category.add_edit_filter', 
                    'service.index', 'service.create', 'service.edit', 'sub-category.index', 'sub-category.create', 'sub-category.edit', 
                    'filters.index', 'filters.create', 'filters.edit','product.discount_settings'
                ];
                ?>
                @can('settings')
                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-sliders-h"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php
                        $subSubMenu = ['sister_concern', 'sister_concern.add', 'sister_concern.edit'];
                        ?>
                        @can('sister_concern')
                        {{-- <li class="nav-item">
                            <a href="{{ route('sister_concern') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Sister Concern</p>
                            </a>
                        </li> --}}
                        @endcan
                        <?php
                        $subSubMenu = ['unit.index', 'unit.create', 'unit.edit'];
                        ?>
                        @can('unit')
                        <li class="nav-item">
                            <a href="{{ route('unit.index') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Units</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['brand.index', 'brand.create', 'brand.edit'];
                        ?>
                        @can('brand')
                        <li class="nav-item">
                            <a href="{{ route('brand.index') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Brands</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['filters.index', 'filters.create', 'filters.edit'];
                        ?>
                        @can('category')
                        <li class="nav-item">
                            <a href="{{ route('filters.index') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Filter</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['category.index', 'category.create', 'category.edit', 'category.add_edit_filter'];
                        ?>
                        @can('category')
                        <li class="nav-item">
                            <a href="{{ route('category.index') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Category</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['sub-category.index', 'sub-category.create', 'sub-category.edit'];
                        ?>
                        @can('sub_category')
                        <li class="nav-item">
                            <a href="{{ route('sub-category.index') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Sub Category</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['product', 'product.add', 'product.edit'];
                        ?>
                        @can('product')
                        <li class="nav-item">
                            <a href="{{ route('product') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Products</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['supplier', 'supplier.add', 'supplier.edit'];
                        ?>
                        @can('supplier')
                        <li class="nav-item">
                            <a href="{{ route('supplier') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Supplier</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['customer', 'customer.add', 'customer.edit'];
                        ?>
                        @can('customer')
                        <li class="nav-item">
                            <a href="{{ route('customer') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Customer</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['product.discount_settings'];
                        ?>
                        @can('product')
                        <li class="nav-item">
                            <a href="{{ route('product.discount_settings') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Discount Setting</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <?php
                $subMenu = ['cash', 'bank', 'bank.add', 'bank.edit', 'branch', 'branch.add', 'branch.edit', 'bank_account', 'bank_account.add', 'bank_account.edit', 'mobile_banking', 'mobile_banking.add', 'mobile_banking.edit'];
                ?>
                @can('bank_and_cash')
                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <i class="nav-icon fa fa-university"></i>
                        <p>
                            Bank & Cash
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php
                        $subSubMenu = ['cash'];
                        ?>
                        @can('cash')
                        <li class="nav-item">
                            <a href="{{ route('cash') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Cash</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['bank', 'bank.add', 'bank.edit'];
                        ?>
                        @can('bank')
                        <li class="nav-item">
                            <a href="{{ route('bank') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Bank</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['branch', 'branch.add', 'branch.edit'];
                        ?>
                        @can('branch')
                        <li class="nav-item">
                            <a href="{{ route('branch') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Branch</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['bank_account', 'bank_account.add', 'bank_account.edit'];
                        ?>
                        @can('bank_account')
                        <li class="nav-item">
                            <a href="{{ route('bank_account') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Account</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['mobile_banking', 'mobile_banking.add', 'mobile_banking.edit'];
                        ?>
                        @can('mobile_banking')
                        <li class="nav-item">
                            <a href="{{ route('mobile_banking') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Mobile Banking</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <?php
                    $menu = ['purchase_order.create','purchase_receipt.edit','category_wise_stock','purchase_inventory.details', 'purchase_receipt.all', 'inventory.all', 'supplier_payments', 'purchase_receipt.details', 'purchase_receipt.qr_code', 'purchase_receipt.bar_code', 'supplier_payment.all', 'purchase_receipt.payment_details', 'batch_wise_inventory.all', 'purchase_receipt.payment_details','purchase_logs'];
                ?>
                @can('purchase')
                <li class="nav-item {{ in_array(Route::currentRouteName(), $menu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $menu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-shopping-bag"></i>
                        <p>
                            Purchase
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php
                        $subSubMenu = ['purchase_order.create'];
                        ?>
                        @can('purchase_order')
                        <li class="nav-item">
                            <a href="{{ route('purchase_order.create') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Purchase Order</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['purchase_receipt.all', 'purchase_receipt.details', 'purchase_receipt.bar_code', 'purchase_receipt.edit'];
                        ?>
                        @can('purchase_receipt')
                        <li class="nav-item">
                            <a href="{{ route('purchase_receipt.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Receipt</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['supplier_payment.all', 'supplier_payments', 'purchase_receipt.payment_details'];
                        ?>
                        @can('supplier_payment')
                        <li class="nav-item">
                            <a href="{{ route('supplier_payment.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Supplier Payment</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['batch_wise_inventory.all','purchase_inventory.details'];
                        ?>
                        @can('batch_wise_stock')
                        <li class="nav-item">
                            <a href="{{ route('batch_wise_inventory.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Batch Wise Stock</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['inventory.all'];
                        ?>
                        @can('stock')
                        <li class="nav-item">
                            <a href="{{ route('inventory.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Stock Report</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <?php
                    $menu = [
                        'sales_order.create', 'sale_receipt_offline.all', 'sale_receipt_online.all', 
                        'sale_receipt.details', 'sale_information.index', 'customer_payment.all', 
                        'sale_receipt.edit', 'sale_receipt.all', 'sales_order.edit', 'sales_order.edit_pos',
                        'customer_payments', 'sale_logs','pre_order.create','pre_order.confirm','pre_order.receipt','pre_order.receipt.details'
                    ];
                ?>
                @can('sales')
                <li class="nav-item {{ in_array(Route::currentRouteName(), $menu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $menu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-shopping-cart"></i>
                        <p>
                            Sale
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php
                        $subSubMenu = ['sales_order.create', 'sale_receipt.edit'];
                        ?>
                        {{-- @can('sales_order')
                        <li class="nav-item">
                            <a href="{{ route('sales_order.create') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Sales Order</p>
                            </a>
                        </li>
                        @endcan --}}
                        <?php
                        $subSubMenu = ['pos'];
                        ?>
                        @can('sales_order')
                        <li class="nav-item">
                            <a href="{{ route('pos') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Sales Order</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['sale_receipt_online.all', 'sales_order.edit'];
                        ?>
                        @can('sales_receipt_online')
                        {{-- <li class="nav-item">
                            <a href="{{ route('sale_receipt_online.all', ['type' => 'online']) }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Receipt Online</p>
                            </a>
                        </li> --}}
                        @endcan
                        <?php
                        $subSubMenu = ['sale_receipt_offline.all', 'sales_order.edit_pos'];
                        ?>
                        @can('sales_receipt_offline')
                        <li class="nav-item">
                            <a href="{{ route('sale_receipt_offline.all', ['type' => 'offline']) }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Sales Receipt(POS)</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['pre_order.create'];
                        ?>
                        @can('sales_order')
                        <li class="nav-item">
                            <a href="{{ route('pre_order.create') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Pre-Order</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['pre_order.receipt','pre_order.receipt.details'];
                        ?>
                        @can('sales_receipt_offline')
                        <li class="nav-item">
                            <a href="{{ route('pre_order.receipt') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Pre-order Receipt</p>
                            </a>
                        </li>
                        @endcan
                        
                        <?php
                        $subSubMenu = ['customer_payment.all','customer_payments'];
                        ?>
                        @can('customer_payment')
                        <li class="nav-item">
                            <a href="{{ route('customer_payment.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Customer Payment</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <?php
                    $menu = ['sales_return_receipt_online.all','sales_return_receipt_offline.all','sales_return_online.create'];
                ?>
                @can('sales_return')
                {{-- <li class="nav-item {{ in_array(Route::currentRouteName(), $menu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $menu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-shopping-cart"></i>
                        <p>
                            Sales Return
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php
                        $subSubMenu = ['sales_return_online.create'];
                        ?>
                        @can('sales_return_order_create')
                        <li class="nav-item">
                            <a href="{{ route('sales_return_online.create') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Sales Return</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['sales_return_receipt_online.all'];
                        ?>
                        @can('sales_return_online_receipt')
                        <li class="nav-item">
                            <a href="{{ route('sales_return_receipt_online.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Return Receipt Online</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['sales_return_receipt_offline.all'];
                        ?>
                        @can('sales_return_offline_receipt')
                        <li class="nav-item">
                            <a href="{{ route('sales_return_receipt_offline.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Return Receipt Offline</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li> --}}
                @endcan
                <?php
                $subMenu = [
                    'ecom_product','ecom_product.add','ecom_product.edit','ecom_product.images','ecom_product.filters',
                    'ecom_banner','ecom_banner.add','ecom_banner.edit','ecom_gallery','ecom_gallery.add','ecom_gallery.edit',
                    'ecom_order','ecom_order.add','ecom_order.edit','ecom_order.details','ecom_order.confirm',
                    'ecom_blog','ecom_blog.add','ecom_blog.edit','ecom_settings.update',
                    'ecom_notice','ecom_notice.add','ecom_notice.edit','ecom_contact','ecom_contact.add','ecom_contact.edit',
                    'ecom_page','ecom_page.add','ecom_page.edit','ecom_coupon','ecom_coupon.add','ecom_coupon.edit',
                ];
                ?>
                {{-- @can('ecommerce')
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-desktop"></i>
                            <p>
                                Ecommerce
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = ['ecom_settings.update'];
                            ?>
                            @can('ecom_settings')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_settings.update',['setting'=>1]) }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Settings</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_notice','ecom_notice.add','ecom_notice.edit'];
                            ?>
                            @can('notices')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_notice') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Notices</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_banner','ecom_banner.add','ecom_banner.edit'];
                            ?>
                            @can('banners')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_banner') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Banners</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_gallery','ecom_gallery.add','ecom_gallery.edit'];
                            ?>
                            @can('gallery')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_gallery') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Gallery</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_page','ecom_page.add','ecom_page.edit'];
                            ?>
                            @can('blogs')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_page') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Pages</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_coupon','ecom_coupon.add','ecom_coupon.edit'];
                            ?>
                            @can('blogs')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_coupon') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Coupons</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_blog','ecom_blog.add','ecom_blog.edit'];
                            ?>
                            @can('blogs')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_blog') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Blogs</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_product','ecom_product.add','ecom_product.edit','ecom_product.images','ecom_product.filters'];
                            ?>
                            @can('product')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_product') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') != 1 ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') != 1 ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Products</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_product','ecom_product.add','ecom_product.edit','ecom_product.images'];
                            ?>
                            @can('product')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_product',['offer'=>1]) }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') == 1 ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') == 1 ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Offer Products</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_order','ecom_order.add','ecom_order.edit','ecom_order.details','ecom_order.confirm'];
                            ?>
                            @can('ecom_orders')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_order') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Orders</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_contact','ecom_contact.add','ecom_contact.edit'];
                            ?>
                            @can('contacts')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_contact') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Contacts</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan --}}

                <?php
                $subMenu = [
                    'account_head.type', 'account_head.type.add', 'account_head.type.edit', 
                    'account_head.sub_type', 'account_head.sub_type.add', 'account_head.sub_type.edit', 
                    'transaction.all', 'transaction.add', 'transaction.details', 'balance_transfer.add'
                ];
                ?>
                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-blog"></i>
                        <p>
                            Accounts
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <?php
                        $subSubMenu = ['account_head.type', 'account_head.type.add', 'account_head.type.edit'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('account_head.type') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Head Type</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['account_head.sub_type', 'account_head.sub_type.add', 'account_head.sub_type.edit'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('account_head.sub_type') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Head Sub Type</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['transaction.all', 'transaction.add', 'transaction.details',];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('transaction.all') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Transaction</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['balance_transfer.add'];
                        ?>
                        <li class="nav-item d-none">
                            <a href="{{ route('balance_transfer.add') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Balance Transfer</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php
                $subMenu = [
                    'product-template.index','product-template.create','product-template.edit',
                    'promotion-template.index','promotion-template.create','promotion-template.edit',
                    'promotion_order','promotion_order.confirm','promotion_order.details',
                ];
                ?>
                @can('ecommerce')
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bullhorn"></i>
                            <p>
                                Promotion
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            
                            <?php
                            $subSubMenu = ['product-template.index','product-template.create','product-template.edit'];
                            ?>
                            @can('ecom_settings')
                                <li class="nav-item d-none">
                                    <a href="{{ route('product-template.index') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Promotion Bundle Temp</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['promotion-template.index','promotion-template.create','promotion-template.edit'];
                            ?>
                            @can('ecom_settings')
                                <li class="nav-item">
                                    <a href="{{ route('promotion-template.index') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Promotion Template</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['promotion_order','promotion_order.confirm','promotion_order.details'];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('promotion_order') }}"
                                    class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Orders</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                
                <?php
                $subMenu = [
                    'ecom_product','ecom_product.add','ecom_product.edit','ecom_product.images','ecom_product.filters',
                    'ecom_banner','ecom_banner.add','ecom_banner.edit','ecom_gallery','ecom_gallery.add','ecom_gallery.edit',
                    'ecom_order','ecom_order.add','ecom_order.edit','ecom_order.details','ecom_order.confirm',
                    'ecom_blog','ecom_blog.add','ecom_blog.edit','ecom_settings.update',
                    'ecom_notice','ecom_notice.add','ecom_notice.edit','ecom_contact','ecom_contact.add','ecom_contact.edit',
                    'ecom_page','ecom_page.add','ecom_page.edit','ecom_coupon','ecom_coupon.add','ecom_coupon.edit',
                ];
                ?>
                @can('ecommerce')
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-desktop"></i>
                            <p>
                                Ecommerce
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = ['ecom_settings.update'];
                            ?>
                            @can('ecom_settings')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_settings.update',['setting'=>1]) }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Settings</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_notice','ecom_notice.add','ecom_notice.edit'];
                            ?>
                            @can('notices')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_notice') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Notices</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_banner','ecom_banner.add','ecom_banner.edit'];
                            ?>
                            @can('banners')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_banner') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Banners</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_gallery','ecom_gallery.add','ecom_gallery.edit'];
                            ?>
                            @can('gallery')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_gallery') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Gallery</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_page','ecom_page.add','ecom_page.edit'];
                            ?>
                            @can('blogs')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_page') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Pages</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_coupon','ecom_coupon.add','ecom_coupon.edit'];
                            ?>
                            @can('blogs')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_coupon') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Coupons</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_blog','ecom_blog.add','ecom_blog.edit'];
                            ?>
                            @can('blogs')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_blog') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Blogs</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_product','ecom_product.add','ecom_product.edit','ecom_product.images','ecom_product.filters'];
                            ?>
                            @can('product')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_product') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') != 1 ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') != 1 ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Products</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_product','ecom_product.add','ecom_product.edit','ecom_product.images'];
                            ?>
                            @can('product')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_product',['offer'=>1]) }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') == 1 ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) && request('offer') == 1 ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Offer Products</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_order','ecom_order.add','ecom_order.edit','ecom_order.details','ecom_order.confirm'];
                            ?>
                            @can('ecom_orders')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_order') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Orders</p>
                                    </a>
                                </li>
                            @endcan
                            <?php
                            $subSubMenu = ['ecom_contact','ecom_contact.add','ecom_contact.edit'];
                            ?>
                            @can('contacts')
                                <li class="nav-item">
                                    <a href="{{ route('ecom_contact') }}"
                                        class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Contacts</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <?php
                $subMenu = [
                    'vat_payment', 'vat_register', 'vat_certificate_six_point_six' 
                ];
                ?>
                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-bullseye"></i>
                        <p>
                            VAT Manage
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        
                        <?php
                        $subSubMenu = ['vat_register'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('vat_register') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Vat Register</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['vat_payment'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('vat_payment') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Vat Payment</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['vat_certificate_six_point_six'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('vat_certificate_six_point_six') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Vat Certificate 6.6</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php
                $subMenu = [
                    'report.purchase','report.product_wise_purchase','report.product_wise_summary',
                    'report.stock_report', 'report.sale','report.sale_report', 'report.balance_sheet', 
                    'report.profit_and_loss','report.bankbook','report.ledger', 'report.transaction', 
                    'report.cashbook', 'selling_product_report', 'report.payment_report',
                    'report.supplier_ledger','report.customer_ledger','report.commission_report',
                    'report.deduction_report','report.pathology_test_report','report.stock_statement'
                ];
                ?>
                @can('reports')
                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-receipt"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php
                        $subSubMenu = ['report.purchase'];
                        ?>
                        @can('purchase_report')
                        <li class="nav-item">
                            <a href="{{ route('report.purchase',['start_date'=>date('d-m-Y'),'end_date'=>date('d-m-Y')]) }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Purchase Report</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.product_wise_purchase'];
                        ?>
                        @can('product_wise_purchase_report')
                        <li class="nav-item">
                            <a href="{{ route('report.product_wise_purchase') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Product Wise Pur. Report</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.product_wise_summary'];
                        ?>
                        @can('product_wise_summary_report')
                        <li class="nav-item">
                            <a href="{{ route('report.product_wise_summary') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Product Summery Report</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.stock_report'];
                        ?>
                        @can('stock_report')
                        <li class="nav-item">
                            <a href="{{ route('report.stock_report') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Stock Report</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.stock_statement'];
                        ?>
                        @can('stock_report')
                        <li class="nav-item">
                            <a href="{{ route('report.stock_statement') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Stock Statement</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.payment_report'];
                        ?>
                        @can('payment_receive_report')
                        <li class="nav-item">
                            <a href="{{ route('report.payment_report') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Payment Receive Report</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.sale'];
                        ?>
                        @can('sales_report')
                        {{-- <li class="nav-item">
                            <a href="{{ route('report.sale') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Functinal Sale Report</p>
                            </a>
                        </li> --}}
                        <?php
                        $subSubMenu = ['report.sale_report'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('report.sale_report') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Sale Report</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['selling_product_report'];
                        ?>
                        @can('selling_product_report')
                        <li class="nav-item d-none">
                            <a href="{{ route('selling_product_report') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Selling Product Report</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.balance_sheet'];
                        ?>
                        <li class="nav-item d-none">
                            <a href="{{ route('report.balance_sheet') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Balance Sheet</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['report.profit_and_loss'];
                        ?>
                        @can('profit_and_loss')
                        <li class="nav-item">
                            <a href="{{ route('report.profit_and_loss',['start'=>date('d-m-Y'),'end'=>date('d-m-Y')]) }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Profit & Loss</p>
                            </a>
                        </li>
                        @endcan
                        <?php
                        $subSubMenu = ['report.ledger'];
                        ?>
                        <li class="nav-item d-none">
                            <a href="{{ route('report.ledger') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Ledger</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['report.supplier_ledger'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('report.supplier_ledger') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Supplier Ledger</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['report.customer_ledger'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('report.customer_ledger') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Customer Ledger</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['report.cashbook'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('report.cashbook') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Cashbook</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['report.bankbook'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('report.bankbook') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Bankbook</p>
                            </a>
                        </li>
                        <?php
                        $subSubMenu = ['report.transaction'];
                        ?>
                        <li class="nav-item">
                            <a href="{{ route('report.transaction') }}"
                                class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                <i
                                    class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                <p>Transaction Report</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
                <?php
                $subMenu = ['sms_panel'];
                ?>
                @can('sms_panel')
                {{-- <li class="nav-item">
                    <a href="{{ route('sms_panel') }}"
                        class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-comment-sms"></i>
                        <p>SMS Panel</p>
                    </a>
                </li> --}}
                @endcan
                <?php
                $subMenu = ['user.index', 'user.create', 'user.edit'];
                ?>
                @can('users')
                <li class="nav-item">
                    <a href="{{ route('user.index') }}"
                        class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-user-tie"></i>
                        <p>Users</p>
                    </a>
                </li> 
                @endcan
            </ul>
            </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->

</aside>
