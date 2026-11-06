@extends('layouts.app')
@section('title', 'Homeo Sales Create')
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
    <form enctype="multipart/form-data" action="{{ route('homeo_sales_order.create') }}" class="form-horizontal" method="post">
        @csrf
        <div class="row">
            <!-- left column -->
            <div class="col-md-9">
                <!-- jquery validation -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Product Information </h3>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('department_id') ? 'has-error' : '' }}">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="department_id" name="department_id" required>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' : '' }}">
                                    <label for="customer">Customer <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="customer" name="customer" required>
                                        <option value="">Search Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="add_product">Product <span class="text-danger">*</span></label>
                                    <input type="text" step="any" class="form-control" id="add_product"
                                        placeholder="product">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="select_unit">Unit <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="select_unit" name="select_unit">
                                        <option value="">Select Unit</option>
                                        @foreach ($units as $unit)
                                            <option {{ old('unit') == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_quantity">Qty <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_quantity"
                                        placeholder="Qty">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_unit_price">Unit Pur Price <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_unit_price"
                                        placeholder="Unit Price">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_selling_price">Unit Pur Price <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_selling_price"
                                        placeholder="Unit Price">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <button type="button" style="margin-top: 31px;" id="add_new_btn"
                                        class="btn btn-primary bg-gradient-primary btn-sm btn-block"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">S/L</th>
                                            <th class="text-center" width="20%">Product</th>
                                            <th class="text-center" width="10%">Unit</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Unit Pur Price</th>
                                            <th class="text-center">Unit Sale Price</th>
                                            <th class="text-center" width="15%">Total Price</th>
                                            <th class="text-center" width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-container">
                                        @if (old('product') != null && sizeof(old('product')) > 0)
                                            @foreach (old('product') as $key => $item)
                                                <tr class="product-item">
                                                    <td class="text-center">
                                                        <span class="product_sl">{{ ++$key }}</span>
                                                    </td>
                                                    <td class="text-left">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('product.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="text"
                                                                value="{{ old('product.' . $loop->index) }}"
                                                                class="form-control text-cenetr product"
                                                                name="product[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-left {{ $errors->has('unit.' . $loop->index) ? 'bg-gradient-danger' : '' }}">
                                                        <span class="unit_name">{{ old('unit_name_val.' . $loop->index) }}</span>
                                                        <input type="hidden" name="unit[]" value="{{ old('unit.' . $loop->index) }}" class="unit">
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('quantity.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('quantity.' . $loop->index) }}"
                                                                class="form-control text-right quantity"
                                                                name="quantity[]">
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
                                                            class="form-group mb-0  {{ $errors->has('selling_price.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('selling_price.' . $loop->index) }}"
                                                                class="form-control text-right selling_price"
                                                                name="selling_price[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right total-selling-cost"></td>
                                                    <td class="text-center"><button type="button"
                                                            class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                                                                class="fa fa-trash-alt"></i></button></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr style="{{ (old('product') != null && sizeof(old('product'))) > 0 ? 'display: revert' : 'display: none' }}" id="footer_area">
                                            <th class="text-right" colspan="3">Total</th>
                                            <th class="text-right" id="total_quantity"></th>
                                            <th class="text-right" id="total_purchase_price"></th>
                                            <th class="text-right" id="total_unit_price"></th>
                                            <th class="text-right" id="total_selling_unit_price"></th>

                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-right">Product Discount (%) </th>
                                            <td>
                                                <div
                                                    class="form-group {{ $errors->has('discount_percentage') ? 'has-error' : '' }}">
                                                    <input type="text" class="form-control" name="discount_percentage"
                                                        id="discount_percentage"
                                                        value="{{ old('discount_percentage', 0) }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-right">Product Discount</th>
                                            <td>
                                                <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                                                    <input type="text" class="form-control" name="discount"
                                                        id="discount"
                                                        value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : '0') : old('discount') }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-right">Total Amount</th>
                                            <th id="total-amount">৳0.00</th>

                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-right">Paid</th>
                                            <td>
                                                <div class="form-group {{ $errors->has('paid') ? 'has-error' : '' }}">
                                                    <input type="text" class="form-control" name="paid"
                                                        id="paid"
                                                        value="{{ empty(old('paid')) ? ($errors->has('paid') ? '' : '0') : old('paid',0) }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-right">Due</th>
                                            <th id="due-amount">৳0.00</th>
                                            <input type="hidden" name="total" id="total">
                                            <input type="hidden" name="due_total" id="due_total">
                                        </tr>
                                        <tr id="tr-next-payment">
                                            <th colspan="6" class="text-right">Next Payment Date</th>
                                            <td>
                                                <div class="form-group {{ $errors->has('next_payment') ? 'has-error' :'' }}">
                                                    <div class="input-group date">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar mt-2"></i>
                                                        </div>
                                                        <input type="text" class="form-control pull-right" id="next_payment" name="next_payment" value="{{ old('next_payment') }}" autocomplete="off">
                                                    </div>
                                                    <!-- /.input group -->
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <select class="form-control select2" id="modal-pay-type" name="payment_type">
                                        <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                                        <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank</option>
                                    </select>
                                </div>

                                <div id="modal-bank-info">
                                    <div class="form-group {{ $errors->has('account') ? 'has-error' :'' }}">
                                        <label> Bank Account</label>
                                        <select class="form-control select2" id="modal-account" name="account">
                                            <option value="">Select Account</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->account_no }} -
                                                    {{ $account->branch->name??'' }} -
                                                    {{ $account->bank->name??'' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="cheque_payment">
                                        <div class="form-group">
                                            <label>Cheque No.</label>
                                            <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No." value="{{ old('cheque_no') }}">
                                        </div>

                                        <div class="form-group {{ $errors->has('cheque_image') ? 'has-error' :'' }}">
                                            <label>Cheque Image</label>
                                            <input class="form-control" name="cheque_image" type="file">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('received_by') ? 'has-error' : '' }}">
                                    <label for="received_by">Received By</label>
                                    <input type="text" value="{{ old('received_by') }}" name="received_by" id="received_by"
                                        class="form-control" placeholder="">
                                    @error('received_by')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
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
                <div class="form-group mb-0">
                    <input type="text" class="form-control text-cenetr product" name="product[]">
                </div>
            </td>
            <td class="text-left">
                <span class="unit_name"></span>
                <input type="hidden" name="unit_name_val[]" class="unit_name_val">
                <input type="hidden" name="unit[]" class="unit">
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right quantity" name="quantity[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right unit_price" name="unit_price[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right selling_price" name="selling_price[]">
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
            //Date picker
            $('#date, #next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            $('#cheque_status').change(function () {
                if ($(this).val() == '1') {
                    $('#cheque_payment').show();
                } else {
                    $('#cheque_payment').hide();
                }
            });

            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });
            $('#modal-pay-type').trigger('change');


            calculate();
            var productIds = [];

            $(".unit").each(function(index) {
                if ($(this).val() != '') {
                    productIds.push($(this).val());
                }
            });
            $('body').on('keypress', '#add_quantity,#add_unit_price,#add_selling_price', function(e) {
                if (e.keyCode == 13) {
                    return false; // prevent the button click from happening
                }
            });
            $('body').on('click', '#add_new_btn', function(e) {
                let selectProduct = $('#select_unit').val();
                let addProduct = $('#add_product').val();
                let addQuantity = $('#add_quantity').val();
                let addUnitPrice = $('#add_unit_price').val();
                let addSellingUnitPrice = $('#add_selling_price').val();

                if (selectProduct == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, select product !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }
                if (addProduct == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, type product name !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }
                if (addQuantity == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, type product quantity !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }
                if (addUnitPrice == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, type product unit price !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }
                if (addSellingUnitPrice == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please, type product selling unit price !',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }

                if (selectProduct != '' && addProduct != '' && addQuantity != '' && addUnitPrice != '' && addSellingUnitPrice !=
                    '') {
                    var addMoreSound = document.getElementById("add_more_sound");
                    addMoreSound.play();
                    var html = $('#template-product').html();
                    var itemHtml = $(html);
                    $('#product-container').prepend(itemHtml);
                    var item = $('.product-item').first();
                    item.hide();
                    item.closest('tr').find('.unit_name').text($("#select_unit option:selected")
                    .text());
                    item.closest('tr').find('.unit_name_val').val($("#select_unit option:selected")
                        .text());
                    item.closest('tr').find('.unit').val($("#select_unit option:selected").val());
                    item.closest('tr').find('.product').val(addProduct);
                    item.closest('tr').find('.quantity').val(addQuantity);
                    item.closest('tr').find('.unit_price').val(addUnitPrice);
                    item.closest('tr').find('.selling_price').val(addSellingUnitPrice);
                    productIds.push(selectProduct);
                    item.show();
                    calculate();
                    $('#select_unit').val(null).trigger('change');
                    $('#add_product').val('');
                    $('#add_quantity').val('');
                    $('#add_unit_price').val('');
                    $('#add_selling_price').val('');
                }
                return false; // prevent the button click from happening
            });

            $('body').on('click', '.btn-remove', function() {
                var product = $(this).closest('tr').find('.unit').val();

                var removeItem = document.getElementById("remove_sound");
                removeItem.play();

                $(this).closest('.product-item').remove();
                calculate();
                productIds = $.grep(productIds, function(value) {
                    return value != product;
                });
            });
            $('body').on('keyup', 'input[type="number"],#discount_percentage,#discount,#paid',
        function() {
                calculate();
            });
            $('body').on('change', 'input[type="number"]', function() {
                calculate();
            });
        })

        function calculate() {
            // Assuming you want to start the product_sl value from 1
            var productSl = 1;

            // Select all the table rows with the class .product-item
            var rows = $("#product-container .product-item");
            // Iterate over each row and update the product_sl value
            rows.each(function() {
                // Find the .product_sl element within the current row
                var productSlElement = $(this).find('.product_sl');
                // Update the text of the .product_sl element with the product_sl value
                productSlElement.text(productSl);
                // Increment the product_sl value for the next iteration
                productSl++;
            });
            var total_quantity = 0;
            var total_selling_price = 0;
            var total_purchase_price = 0;
            var discount = parseFloat($('#discount').val() || 0);
            var paid = parseFloat($('#paid').val() || 0);
            $('.product-item').each(function(i, obj) {

                let quantity = parseFloat($('.quantity:eq(' + i + ')').val());
                quantity = (isNaN(quantity) || quantity < 0) ? 0 : quantity;

                let unit_price = parseFloat($('.unit_price:eq(' + i + ')').val());
                unit_price = (isNaN(unit_price) || unit_price < 0) ? 0 : unit_price;

                let selling_price = parseFloat($('.selling_price:eq(' + i + ')').val());
                selling_price = (isNaN(selling_price) || selling_price < 0) ? 0 : selling_price;


                $('.total-selling-cost:eq(' + i + ')').text((quantity * selling_price).toFixed(2));
                total_quantity += quantity;
                total_selling_price += quantity * selling_price;
                total_purchase_price += quantity * unit_price;
            });

            $('#total_quantity').text(total_quantity);
            $('#total_selling_unit_price').text(total_selling_price.toFixed(2));
            $('#total_purchase_price').text(total_purchase_price.toFixed(2));

            if ($('#discount_percentage').val() >= 0) {
                discount = ($('#discount_percentage').val() * total_purchase_price) / 100;
                $('#discount').val(discount);

            } else {
                $('#discount_percentage').val(0);
            }

            var total = parseFloat(total_selling_price) - discount;
            $('#total-amount').html('৳' + total.toFixed(2));
            $('#total').val(total.toFixed(2));
            var due_amount = parseFloat(total) - parseFloat(paid);;
            $('#due-amount').html('৳' + due_amount.toFixed(2));
            $('#due_total').val(due_amount.toFixed(2));

            if (due_amount > 0) {
                $('#tr-next-payment').show();
            } else {
                $('#tr-next-payment').hide();
            }
            if (rows.length > 0) {
                $("#footer_area").show();
            } else {
                $("#footer_area").hide();
            }
        }
    </script>
@endsection
