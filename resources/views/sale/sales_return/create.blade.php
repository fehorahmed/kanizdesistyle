@extends('layouts.app')
@section('title', 'Sales Return')
@section('style')
    <style>
        .table td,
        .table th {
            padding: 6px;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')

    <!-- form start -->
    <form enctype="multipart/form-data" id="saleReturnForm" action="{{ route('sales_return_fm_online.create') }}" class="form-horizontal" method="post">
        @csrf
        <div class="row">
            <!-- left column -->
            <div class="col-md-9">
                <!-- jquery validation -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Order Information </h3>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('sale_type') ? 'has-error' : '' }}">
                                    <label for="sale_type">Sale Type <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="sale_type" name="sale_type" required>
                                        <option value="1" {{ old('sale_type') == '1'?'selected':'' }}>Offline</option>
                                        <option value="2" {{ old('sale_type') == '2'?'selected':'' }}>Online</option>
                                    </select>
                                    @error('sale_type')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' : '' }}">
                                    <label for="customer">Customer <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="customer" name="customer" required>
                                        <option value="">Search Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}-{{ $customer->mobile_no }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('order') ? 'has-error' : '' }}">
                                    <label for="order">Order No <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="order" name="order" required>
                                        <option value="">Select Order</option>
                                    </select>
                                    @error('order')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">S/L</th>
                                            <th class="text-center" width="20%">Item Details</th>
                                            {{-- <th class="text-center">Batch</th> --}}
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Return Qty</th>
                                            <th class="text-center">Dis(%)</th>
                                            <th class="text-center">Deduct(%)</th>
                                            <th class="text-center">Deduct Amt</th>
                                            <th class="text-center">Selling Price</th>
                                            <th class="text-center">Dis Amt</th>
                                            <th class="text-center">Return Price</th>
                                            <th class="text-center">Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previous-product-container">
                                        @if (old('product') != null && sizeof(old('product')) > 0)
                                            @foreach (old('product') as $key => $item)
                                                <tr class="previous-product-item">
                                                    <td class="text-center">
                                                        <span class="product_sl">{{ ++$key }}</span>
                                                    </td>
                                                    <td
                                                        class="text-left {{ $errors->has('product.' . $loop->index) ? 'bg-gradient-danger' : '' }}">
                                                        <span
                                                            class="product_name">{{ old('product_name_val.' . $loop->index) }}</span>
                                                        <input type="hidden" name="product_name_val[]"
                                                            value="{{ old('product_name_val.' . $loop->index) }}"
                                                            class="product_name_val">
                                                        <input type="hidden" name="product[]"
                                                            value="{{ old('product.' . $loop->index) }}" class="product">
                                                        <input type="hidden" name="serial[]"
                                                            value="{{ old('serial.' . $loop->index) }}"
                                                            class="product_code">
                                                        <input type="hidden" value="{{ old('product_id.' . $loop->index) }}" name="product_id[]" class="product_id">
                                                        <input type="hidden" value="{{ old('batch.' . $loop->index) }}" name="batch[]" class="batch">
                                                        <input type="hidden" value="{{ old('expiry_date.' . $loop->index) }}" name="expiry_date[]" class="expiry_date">
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('pack_quantity.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('pack_quantity.' . $loop->index) }}"
                                                                class="form-control text-right pack_quantity"
                                                                name="pack_quantity[]" readonly>
                                                        </div>
                                                        <input type="hidden" step="any"
                                                            value="{{ old('pack_size.' . $loop->index) }}"
                                                            class="form-control text-right pack_size" name="pack_size[]">
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('return_quantity.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('return_quantity.' . $loop->index) }}"
                                                                class="form-control text-right return_quantity"
                                                                name="return_quantity[]">
                                                        </div>
                                                    </td>
   
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('discount_percentage.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('discount_percentage.' . $loop->index) }}"
                                                                class="form-control text-right discount_percentage"
                                                                name="discount_percentage[]">
                                                        </div>
                                                    </td>
   
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('deduct_percent.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('deduct_percent.' . $loop->index) }}"
                                                                class="form-control text-right deduct_percent"
                                                                name="deduct_percent[]">
                                                        </div>
                                                    </td>
   
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('deduct_amt.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('deduct_amt.' . $loop->index) }}"
                                                                class="form-control text-right deduct_amt"
                                                                name="deduct_amt[]">
                                                        </div>
                                                    </td>
   
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('unit_price.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('unit_price.' . $loop->index) }}"
                                                                class="form-control text-right unit_price"
                                                                name="unit_price[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('discount_amt.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('discount_amt.' . $loop->index) }}"
                                                                class="form-control text-right discount_amt"
                                                                name="discount_amt[]" readonly>
                                                            
                                                            <input type="hidden" name="return_discount_amt[]" class="return_discount_amt" value="{{ old('return_discount_amt.' . $loop->index) }}"/>    
                                                        </div>
                                                    </td>
                                                    <td class="text-right total-return-cost">0</td>
                                                    <td class="text-right total_selling_cost"></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="10" class="text-right">Previous Sub Total</th>
                                            <th class="text-right" id="previous-sub-total">৳0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-right">Previous Discount(-)</th>
                                            <th class="text-right" id="previous-dis-amount">৳0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-right">Previous Total</th>
                                            <th class="text-right" id="previous_total">৳0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-right">Return(-)</th>
                                            <th class="text-right" id="return-amount">৳0.00</th>
                                            <input type="hidden" name="total_return" id="total_return"/>
                                            <input type="hidden" name="total_return_discount" id="total_return_discount"/>
                                        </tr>
                                        {{-- <tr>
                                            <th colspan="10" class="text-right">Return Discount(-)</th>
                                            <th class="text-right" id="return-discount">৳0.00</th>
                                        </tr> --}}
                                        <tr>
                                            <th colspan="10" class="text-right">Deduction(+)</th>
                                            <th class="text-right" id="return-deduction">৳0.00</th>
                                            <input type="hidden" name="total_return_deduction" id="total_return_deduction"/>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-right">Total Payable Amt</th>
                                            <th class="text-right" id="total_payable_amount_text">৳0.00</th>
                                            <input type="hidden" id="total_payable_amount"/>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-right">Previous Paid</th>
                                            <th class="text-right">
                                                <div class="form-group {{ $errors->has('previous_paid') ? 'has-error' : '' }}">
                                                    <input type="text" readonly class="form-control text-right" name="previous_paid" id="previous_paid" value="{{ old('previous_paid', 0) }}">
                                                </div>
                                            </th>
                                        </tr>
                                        {{-- <tr>
                                            <th colspan="10" class="text-right" id="due_advance_text">Due</th>
                                            <th class="text-right" id="previous-due-amount">৳0.00</th>
                                            <input type="hidden" name="total" id="total">
                                            <input type="hidden" name="previous_due_total" id="previous_due_total">
                                        </tr> --}}
                                    </tfoot>
                                    
                                </table>
                                <table class="table table-bordered">
                                    <tbody id="return_order_payments">
                                        @if (old('payment_id') != null && sizeof(old('payment_id')) > 0)
                                            @foreach (old('payment_id') as $key => $item)
                                                <tr class="return_order_payment_row">
                                                    <th class="text-right">
                                                        <input type="hidden" name="payment_method_name[]" value="{{ old('payment_method_name.' . $loop->index) }}"/>
                                                        <input type="hidden" name="payment_method_desc[]" value="{{ old('payment_method_desc.' . $loop->index) }}"/>
                                                        <input type="hidden" name="payment_id[]" value="{{ old('payment_id.' . $loop->index) }}"/>
                                                    </th>
                                                    <th class="text-right">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control payment_amount text-right" name="payment_amount[]" value="{{ old('payment_amount.' . $loop->index) }}">
                                                        </div>
                                                    </th>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <input type="hidden" name="total_payment_amount" id="total_payment_amount"/>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="button" id="sale_return_submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
                        <a href="" class="btn btn-danger bg-gradient-danger btn-sm float-right">Cancel</a>
                    </div>
                    <!-- /.card-footer -->

                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-3">
                <!-- jquery validation -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Other Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" value="{{ old('date', date('d-m-Y')) }}"
                                        name="date" id="date" class="form-control date-picker"
                                        placeholder="Enter date">
                                    @error('date')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                <div class="form-group {{ $errors->has('received_by') ? 'has-error' : '' }}">
                                    <label for="received_by">Received By</label>
                                    <input type="text" value="{{ old('received_by') }}" name="received_by"
                                        id="received_by" class="form-control" placeholder="">
                                    @error('received_by')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                                    <label for="notes">Notes</label>
                                    <input type="text" value="{{ old('notes') }}" name="notes" id="notes"
                                        class="form-control" placeholder="Enter Notes">
                                    @error('notes')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer"></div>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>
            <!--/.col (left) -->
        </div>
    </form>

    <template id="template-product">
        <tr class="product-item">
            <td class="text-center">
                <span class="product_sl"></span>
            </td>
            <td class="text-left">
                <span class="product_name"></span>
                <input type="hidden" name="product_name_val[]" class="product_name_val">
                <input type="hidden" name="product[]" class="product">
            </td>
            <td class="text-center">
                <div class="form-group mb-0">
                    <input type="text" class="form-control text-right batch" name="batch[]" readonly>
                </div>
            </td>
            <td class="text-center">
                <div class="form-group mb-0">
                    <input type="text" class="form-control text-center expiry_date" name="expiry_date[]" readonly>
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right stock" name="stock[]" readonly>
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right pack_quantity"
                        name="pack_quantity[]">
                </div>
                <input type="hidden" step="any" class="form-control text-right pack_size" name="pack_size[]">
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right discount" name="discount[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right unit_price" name="unit_price[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right discount_amt"
                        name="discount_amt[]" readonly>
                </div>
            </td>
            <td class="text-right total-selling-cost"></td>
            <td class="text-center"><button type="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                        class="fa fa-trash-alt"></i></button></td>
        </tr>
    </template>
@endsection
@section('script')
    <script>
        $(function() {

            $('body').on('click', '#sale_return_submit', function() {
                let total_payable_amount = $('#total_payable_amount').val();
                let total_payment_amount = $('#total_payment_amount').val();
                if(total_payable_amount < total_payment_amount){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Payment Amount',
                        text: 'Payment Amount must be less than or equal to Total Payable amount!'
                    });
                    let notificationSound = document.getElementById('notification-error-audio');
                    notificationSound?.play();
                    return false;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You want to save it!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#saleReturnForm').submit();
                    }
                });
            });
            
            $('.select2').select2();
            var old_orderId = '{{ old('order') }}';
            $('#customer,#sale_type').change(function(){
                var sale_type = $('#sale_type').val();
                var customer = $('#customer').val();

                $('#previous-product-container').html('');
                $('#return_order_payments').html('');

                $('#order').html('<option value="">Select Order</option>');
                if(customer != '' && sale_type != ''){
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_customer_sales_order') }}",
                        data: {
                            sale_type: sale_type,
                            customer: customer,
                        },
                    }).done(function(response) {
                        if (response) {
                            if(old_orderId == response.id){
                                $('#order').append('<option value="'+ response.id +'" selected>'+ response.order_no +'</option>');
                            }else{
                                $('#order').append('<option value="'+ response.id +'">'+ response.order_no +'</option>');
                            }
                        }
                    });
                }
            });
            $('#customer').trigger('change');
            
            $('#order').change(function(){
                var orderId = $(this).val();
                // $('#order').html('<option value="">Select Order</option>');
                $('#previous-product-container').html('');
                $('#return_order_payments').html('');
                if(customer){
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_sales_order_items') }}",
                        data: {
                            orderId: orderId,
                        },
                    }).done(function(response) {
                        if (response) {
                            $('#previous-product-container').html(response.html);
                            $('#return_order_payments').html(response.orderPaymentsHtml);
                            $('#previous_paid').val(response.order.paid);
                            order_calculate();
                        }
                    });
                }
            });
            // Initialize datepickers
            $('#date, #next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
            });

            // Toggle cheque payment section
            $('#cheque_status').change(function() {
                if ($(this).val() == '1') {
                    $('#cheque_payment').show();
                } else {
                    $('#cheque_payment').hide();
                }
            });

            // Toggle modal bank info section
            $('#modal-pay-type').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            }).trigger('change');

            // Reset product-related fields on department change
            $('body').on('change', '#department_id', function() {
                $('#add_stock, #add_unit_price,#add_pack_size').val('');
                $('#select_product').val(null).trigger('change');
            });

            calculate();
            order_calculate();
            // Keep track of selected products
            let productIds = [];
            $(".product").each(function() {
                if ($(this).val() != '') {
                    productIds.push($(this).val());
                }
            });

            // Prevent default action for Enter key on numeric inputs
            $('body').on('keypress', '#add_pack_quantity, #add_discount, #add_unit_price,#add_pack_size', function(
                e) {
                if (e.keyCode == 13) return false;
            });

            // Handle product selection
            $('body').on('change', '#select_product', function() {
                let departmentId = $('#department_id').val();

                if (!departmentId) {
                    alert('Please select a Department first!');
                    return;
                }

                let productId = $(this).val();
                $('#add_unit_price,#add_expiry_date,#add_stock,#add_pack_size').val('');
                $('#add_batch').empty().append('<option value="">Search Batch</option>');

                if (productId) {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_inventory') }}",
                        data: {
                            id: productId,
                            department_id: departmentId
                        },
                    }).done(function(response) {
                        if (response) {
                            $('#add_stock').val(response.pack_quantity || 0);
                            $('#add_unit_price').val(response.selling_price || 0);

                            if (response.batches.length > 0) {
                                response.batches.forEach(batch => {
                                    $('#add_batch').append(
                                        `<option value="${batch}">Batch-${batch}</option>`
                                    );
                                });
                            }
                        } else {
                            $('#add_stock').val(0);
                        }
                    });
                }
            });

            // Handle batch selection
            $('body').on('change', '#add_batch', function() {
                let departmentId = $('#department_id').val();
                let productId = $('#select_product').val();

                if (!departmentId || !productId) {
                    alert('Department or product not found!');
                    return;
                }

                let batchId = $(this).val();
                $('#add_unit_price,#add_expiry_date,#add_stock').val('');

                if (batchId) {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_inventory_batch_wise') }}",
                        data: {
                            id: batchId,
                            product_id: productId,
                            department_id: departmentId
                        },
                    }).done(function(response) {
                        if (response) {
                            $('#add_expiry_date').val(response.expiry_date || '');
                            $('#add_stock').val(response.pack_quantity || 0);
                            $('#add_pack_size').val(response.pack_size || 0);
                            $('#add_unit_price').val(response.selling_price || 0);
                        } else {
                            $('#add_stock').val(0);
                        }
                    });
                }
            });

            // Add new product row
            $('body').on('click', '#add_new_btn', function() {
                let selectProduct = $('#select_product').val();
                let selectProductName = $("#select_product option:selected").text();
                let addBatch = $('#add_batch').val();
                let addExpiryDate = $('#add_expiry_date').val();
                let addStock = parseFloat($('#add_stock').val()) || 0; // Stock pack_quantity
                let addQuantity = parseFloat($('#add_pack_quantity').val()) || 0; // Entered pack_quantity
                let addPackSize = parseFloat($('#add_pack_size').val()) || 0; // Entered pack_quantity
                let addDiscount = parseFloat($('#add_discount').val()) || 0; // Discount pack_quantity
                let addUnitPrice = $('#add_unit_price').val();

                if (!validateInputs(selectProduct, addQuantity, addUnitPrice, addBatch)) {
                    return false;
                }

                // Check if entered pack_quantity exceeds stock
                if (addQuantity > addStock) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock Exceeded',
                        text: `The entered pack_quantity (${addQuantity}) exceeds the available stock (${addStock})! Please select another batch.`
                    });
                    let notificationSound = document.getElementById('notification-error-audio');
                    notificationSound?.play();
                    return false;
                }

                // Check for duplicates
                let isDuplicate = false;
                $("#product-container .product-item").each(function() {
                    let existingProduct = $(this).find('.product').val();
                    let existingBatch = $(this).find('.batch').val().replace('Batch-',
                        ''); // Remove "Batch-" prefix

                    if (existingProduct === selectProduct && existingBatch === addBatch) {
                        isDuplicate = true;
                        return false; // Exit loop
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Entry',
                        text: 'This product and batch are already added!'
                    });
                    let notificationSound = document.getElementById('notification-error-audio');
                    notificationSound?.play();
                    return false;
                }

                let addMoreSound = document.getElementById("add_more_sound");
                addMoreSound.play();

                let html = $('#template-product').html();
                let itemHtml = $(html);
                $('#product-container').prepend(itemHtml);

                let item = $('.product-item').first();
                populateProductRow(item, selectProductName, selectProduct, addBatch, addExpiryDate,
                    addStock, addQuantity, addPackSize, addDiscount, addUnitPrice);

                productIds.push(selectProduct);
                resetProductFields();
                calculate();
            });


            // Remove product row
            $('body').on('click', '.btn-remove', function() {
                let product = $(this).closest('tr').find('.product').val();

                let removeSound = document.getElementById("remove_sound");
                removeSound.play();

                $(this).closest('.product-item').remove();
                productIds = productIds.filter(id => id != product);
                calculate();
            });

            // Update calculations on input change
            $('body').on('keyup change', 'input[type="number"], #discount_percentage, #discount, #paid',
                function() {
                    calculate();
                });
            // Update calculations on input change
            $('body').on('keyup change', '.return_quantity,.payment_amount',
                function() {
                    order_calculate();
            });
        });

        function validateInputs(selectProduct, addQuantity, addUnitPrice, addBatch) {
            let errors = [];

            if (!selectProduct) errors.push('Please, select product!');
            if (!addQuantity) errors.push('Please, type product pack_quantity!');
            if (!addUnitPrice) errors.push('Please, type product unit price!');
            if (!addBatch) errors.push('Please, select batch!');

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errors[0]
                });
                let notificationSound = document.getElementById('notification-error-audio');
                notificationSound?.play();
                return false;
            }

            return true;
        }

        function populateProductRow(item, name, id, batch, expiryDate, stock, pack_quantity, pack_size, discount,
            unitPrice) {
            item.hide();
            item.find('.product_name').text(name);
            item.find('.product_name_val').val(name);
            item.find('.product').val(id);
            item.find('.batch').val('Batch-' + batch);
            item.find('.expiry_date').val(expiryDate);
            item.find('.stock').val(stock);
            item.find('.pack_quantity').val(pack_quantity);
            item.find('.pack_size').val(pack_size);
            item.find('.discount').val(discount);
            item.find('.unit_price').val(unitPrice);
            item.show();
        }

        function resetProductFields() {
            $('#select_product').val(null).trigger('change');
            $('#add_batch, #add_stock,#add_expiry_date,#add_pack_quantity,#add_discount, #add_unit_price,#add_pack_size')
                .val('');
        }

        function calculate() {
            let productSl = 1;
            let totalQuantity = 0;
            let totalDisPrice = 0;
            let totalSellingPrice = 0;
            let discount = parseFloat($('.discount').val()) || 0;
            let paid = parseFloat($('#paid').val()) || 0;

            $("#product-container .product-item").each(function() {
                $(this).find('.product_sl').text(productSl++);

                let pack_quantity = parseFloat($(this).find('.pack_quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                let productDiscount = parseFloat($(this).find('.discount').val()) ||
                0; // per product discount

                $(this).find('.total-selling-cost').text((pack_quantity * unitPrice).toFixed(2));

                // Use the individual product discount if present
                let discountAmt = productDiscount > 0 ?
                    (pack_quantity * (productDiscount * unitPrice) / 100) :
                    0;

                $(this).find('.discount_amt').val(discountAmt.toFixed(2));

                totalQuantity += pack_quantity;
                totalSellingPrice += pack_quantity * unitPrice;
                totalDisPrice += discountAmt;
            });

            $('#total_pack_quantity').text(totalQuantity);
            $('#total_dis_price').text(Math.floor(totalDisPrice));
            $('#total_selling_price').text(Math.ceil(totalSellingPrice));
            $('#sub_total').val(Math.ceil(totalSellingPrice));

            let total = Math.ceil(totalSellingPrice - Math.floor(totalDisPrice));
            $('#total-dis-amount').html('৳' + Math.floor(totalDisPrice));
            $('#total-amount').html('৳' + total.toFixed(2));
            $('#total').val(total.toFixed(2));
            $('#previous_total').html('৳' + total.toFixed(2));

            let dueAmount = Math.ceil(total - paid);
            $('#due-amount').html('৳' + dueAmount.toFixed(2));
            $('#due_total').val(dueAmount.toFixed(2));

            $('#tr-next-payment').toggle(dueAmount > 0);
            $("#footer_area").toggle($("#product-container .product-item").length > 0);
        }
        function order_calculate() {
            let productSl = 1;
            let totalQuantity = 0;
            let totalDisPrice = 0;
            let totalReturnDisPrice = 0;
            let totalReturnDeductionPrice = 0;
            let totalSellingPrice = 0;
            let totalReturnPrice = 0;
            let previous_paid = parseFloat($('#previous_paid').val()) || 0;
            let return_paid = parseFloat($('#return_paid').val()) || 0;

            $("#previous-product-container .previous-product-item").each(function() {
                // $(this).find('.product_sl').text(productSl++);
                let pack_quantity = parseFloat($(this).find('.pack_quantity').val()) || 0;
                let return_quantity = parseFloat($(this).find('.return_quantity').val()) || 0;
                

                if(return_quantity > pack_quantity){
                    $(this).find('.return_quantity').val(1);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Return Quantity',
                        text: 'Return Quantity must be less than or equal Order Quantity!'
                    });
                    let notificationSound = document.getElementById('notification-error-audio');
                    notificationSound?.play();
                    return false;
                }
            });   

            $("#previous-product-container .previous-product-item").each(function() {
                // $(this).find('.product_sl').text(productSl++);
                
                let pack_quantity = parseFloat($(this).find('.pack_quantity').val()) || 0;
                let return_quantity = parseFloat($(this).find('.return_quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                let discount_percent = parseFloat($(this).find('.discount_percentage').val()) || 0; // per product discount
                let productDiscount = parseFloat($(this).find('.discount_amt').val()) || 0; // per product discount
                let deduct_percent = parseFloat($(this).find('.deduct_percent').val()) || 0;
                let returnDiscountAmt = discount_percent > 0 ?
                                    (return_quantity * (discount_percent * unitPrice) / 100) :
                                    0;
                let returnDeductionAmt = deduct_percent > 0 ?
                                    (return_quantity * (deduct_percent * unitPrice) / 100) :
                                    0;

                $(this).find('.return_discount_amt').val(returnDiscountAmt.toFixed(2));
                $(this).find('.deduct_amt').val((returnDeductionAmt).toFixed(2));
                $(this).find('.total_selling_cost').text((pack_quantity * unitPrice).toFixed(2));
                $(this).find('.total-return-cost').text(((return_quantity * unitPrice)-returnDiscountAmt).toFixed(2));
                
                totalDisPrice += productDiscount;
                totalReturnDisPrice += returnDiscountAmt;
                totalReturnDeductionPrice += returnDeductionAmt;
                totalSellingPrice += pack_quantity * unitPrice;
                totalReturnPrice += (return_quantity * unitPrice)-returnDiscountAmt;
            });
            
            totalDisPrice = Math.floor(totalDisPrice);
            totalReturnDisPrice = Math.floor(totalReturnDisPrice);
            totalReturnDeductionPrice = Math.floor(totalReturnDeductionPrice);
            totalReturnPrice = Math.ceil(totalReturnPrice);

            // totalDisPrice = (totalDisPrice - totalReturnDisPrice);
            let total = Math.ceil(totalSellingPrice - totalDisPrice);

            $('#previous-dis-amount').text('৳' + totalDisPrice.toFixed(2));
            $('#total_return_discount').text('৳' + totalReturnDisPrice.toFixed(2));
            $('#return-discount').text('৳' + totalReturnDisPrice.toFixed(2));
            $('#total_return').val(totalReturnPrice.toFixed(2));
            $('#return-amount').text('৳' + totalReturnPrice.toFixed(2));
            $('#previous-sub-total').text('৳' + totalSellingPrice.toFixed(2));
            $('#previous_total').html('৳' + total.toFixed(2));

            $('#return-deduction').text('৳' + totalReturnDeductionPrice.toFixed(2));
            $('#total_return_deduction').val(totalReturnDeductionPrice.toFixed(2));

            var total_amount = total - totalReturnPrice + totalReturnDeductionPrice;
            $('#total_payable_amount_text').text('৳' + total_amount.toFixed(2));
            $('#total_payable_amount').val(total_amount.toFixed(2));

            let total_payment_amount = 0;
            $("#return_order_payments .return_order_payment_row").each(function() {
                let payment_amount = parseFloat($(this).find('.payment_amount').val()) || 0;

                total_payment_amount += payment_amount;
            });

            $('#total_payment_amount').val(total_payment_amount);
            console.log(total_payment_amount);
        }
    </script>

@endsection
