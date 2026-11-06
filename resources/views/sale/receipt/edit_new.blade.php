@extends('layouts.app')
@section('title', 'Sales Edit')
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
    <form enctype="multipart/form-data" action="{{ route('sale_receipt.edit',['order'=>$order->id]) }}" class="form-horizontal" method="post">
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
                                    <select class="form-control select2" id="department_id" name="department_id">
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id',$order->department_id) == $department->id ? 'selected' : '' }}>
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
                                    <select class="form-control select2" id="customer" name="customer">
                                        <option value="">Search Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer',$order->customer_id) == $customer->id ? 'selected' : '' }}>
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
                                    <label for="select_product">Product <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="select_product" name="select_product">
                                        <option value="">Search Name of Product</option>
                                        @foreach ($products as $product)
                                            <option {{ old('product') == $product->id ? 'selected' : '' }}
                                                value="{{ $product->id }}" data-code="{{ $product->code }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_stock">Stock <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_stock"
                                        placeholder="Stock" readonly>
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
                                    <label for="add_exchange_quantity">Exchange</label>
                                    <input type="number" step="any" class="form-control" id="add_exchange_quantity"
                                        placeholder="Qty" value="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="add_unit_price">Unit Price <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="add_unit_price"
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
                                            <th class="text-center" width="20%">Item Details</th>
                                            {{-- <th class="text-center">Stock</th> --}}
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Exchange</th>
                                            <th class="text-center">Unit Price</th>
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
                                                            value="{{ old('serial.' . $loop->index) }}" class="product_code">
                                                    </td>
                                                    {{-- <td class="text-right">
                                                        <div
                                                            class="form-group mb-0  {{ $errors->has('stock.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('stock.' . $loop->index) }}"
                                                                class="form-control text-right stock"
                                                                name="stock[]" readonly>
                                                        </div>
                                                    </td> --}}
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
                                                            class="form-group mb-0  {{ $errors->has('exchange_qty.' . $loop->index) ? 'has-error' : '' }}">
                                                            <input type="number" step="any"
                                                                value="{{ old('exchange_qty.' . $loop->index) }}"
                                                                class="form-control text-right exchange_qty"
                                                                name="exchange_qty[]">
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
                                                    <td class="text-right total-purchase-cost"></td>
                                                    <td class="text-center"><button type="button"
                                                            class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                                                                class="fa fa-trash-alt"></i></button></td>
                                                </tr>
                                            @endforeach
                                        @else

                                            @foreach ($order->products as $key => $product)
                                                <tr class="product-item">
                                                    <td class="text-center">
                                                        <span class="product_sl">{{ ++$key }}</span>
                                                    </td>
                                                    <td
                                                        class="text-left">
                                                        <span
                                                            class="product_name">{{ $product->pivot->name }}</span>
                                                        <input type="hidden" name="product_name_val[]"
                                                            value="{{ $product->pivot->name }}"
                                                            class="product_name_val">
                                                        <input type="hidden" name="product[]"
                                                            value="{{ $product->pivot->id }}" class="product">
                                                        <input type="hidden" name="serial[]"
                                                            value="{{ $product->pivot->serial }}" class="product_code">
                                                    </td>
                                                    {{-- <td class="text-right">
                                                        <div
                                                            class="form-group mb-0">
                                                            <input type="number" step="any"
                                                                value=""
                                                                class="form-control text-right stock"
                                                                name="stock[]" readonly>
                                                        </div>
                                                    </td> --}}
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0">
                                                            <input type="number" step="any"
                                                                value="{{ $product->pivot->quantity }}"
                                                                class="form-control text-right quantity"
                                                                name="quantity[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0">
                                                            <input type="number" step="any"
                                                                value="{{ $product->pivot->exchange_qty }}"
                                                                class="form-control text-right exchange_qty"
                                                                name="exchange_qty[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <div
                                                            class="form-group mb-0">
                                                            <input type="number" step="any"
                                                                value="{{ $product->pivot->unit_price }}"
                                                                class="form-control text-right unit_price"
                                                                name="unit_price[]">
                                                        </div>
                                                    </td>
                                                    <td class="text-right total-purchase-cost"></td>
                                                    <td class="text-center"><button type="button"
                                                            class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                                                                class="fa fa-trash-alt"></i></button></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr style="{{ (old('product') != null && sizeof(old('product'))) > 0 ? 'display: revert' : 'display: none' }}"
                                            id="footer_area">
                                            <th class="text-right" colspan="2">Total</th>
                                            <th class="text-right" id="total_quantity"></th>
                                            <th class="text-right" id="total_unit_price"></th>
                                            <th class="text-right" id="total_purchase_price"></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <td>

                                            </td>
                                            <th colspan="4" class="text-right"> TVA (%)</th>
                                            <th>
                                                <input type="number" name="vat" id="tva_percentage"
                                                    class="form-control" value="{{ old('vat',$order->vat_percentage) }}">
                                                <span id="tva"></span>
                                            </th>
                                        </tr>

                                        <tr>
                                            <th colspan="5" class="text-right">Product Discount</th>
                                            <td>
                                                <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                                                    <input type="text" class="form-control" name="discount"
                                                        id="discount"
                                                        value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : '0') : old('discount',$order->discount) }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>

                                            </td>
                                            <th colspan="4" class="text-right">Total Amount</th>
                                            <th id="total-amount">৳0.00</th>
                                            <input type="hidden" name="total" id="total">
                                            <input type="hidden" name="paid" value="0">
                                            <input type="hidden" name="payment_type" value="1">
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
                                    <input type="text" autocomplete="off" value="{{ old('date', date('d-m-Y',strtotime($order->date))) }}"
                                        name="date" id="date" class="form-control date-picker"
                                        placeholder="Enter date">
                                    @error('date')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('received_by') ? 'has-error' : '' }}">
                                    <label for="received_by">Received By</label>
                                    <input type="text" value="{{ old('received_by',$order->received_by) }}" name="received_by" id="received_by"
                                        class="form-control" placeholder="">
                                    @error('received_by')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                                    <label for="notes">Notes</label>
                                    <input type="text" value="{{ old('notes',$order->notes) }}" name="notes" id="notes"
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
                <input type="hidden" name="serial[]" class="product_code">
            </td>
            {{-- <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right stock" name="stock[]" readonly>
                </div>
            </td> --}}
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right quantity" name="quantity[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right exchange_qty" name="exchange_qty[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group mb-0">
                    <input type="number" step="any" class="form-control text-right unit_price" name="unit_price[]">
                </div>
            </td>
            <td class="text-right total-purchase-cost"></td>
            <td class="text-center"><button type="button" class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i
                        class="fa fa-trash-alt"></i></button></td>
        </tr>
    </template>

@endsection
@section('script')
    <script>
        $(function() {
            $('#department').on('change', function() {
                // Get the selected Department ID
                var departmentId = $(this).val();

                // Construct the new URL
                var currentUrl = window.location.href.split('?')[
                0]; // Get the base URL without query parameters
                var newUrl = currentUrl + '?department=' + departmentId;

                // Update the browser's URL and reload the page
                window.location.href = newUrl;
            });


            calculate();
            var productIds = [];

            $(".product").each(function(index) {
                if ($(this).val() != '') {
                    productIds.push($(this).val());
                }
            });
            $('body').on('keypress', '#add_quantity,#add_warranty,#add_unit_price,#add_selling_price', function(e) {
                if (e.keyCode == 13) {
                    return false; // prevent the button click from happening
                }
            });
            $('body').on('change', '#select_product', function(e) {
                let selectProductId = $(this).val();
                let department_id = $('#department_id').val();
                $('#add_unit_price').val(' ');
                if (selectProductId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_product_inventory') }}",
                        data: {
                            id: selectProductId,
                            department_id: department_id,
                        }
                    }).done(function(response) {
                        if(response){
                            $('#add_stock').val(response.quantity);
                            $('#add_unit_price').val(response.selling_price);
                        }else{
                            $('#add_stock').val(0);
                            //$('#add_unit_price').val(response.selling_price);
                        }
                        //$('#add_selling_price').val(response.selling_price);
                    });
                }
            });

            $('body').on('click', '#add_new_btn', function(e) {
                let selectProduct = $('#select_product').val();
                let selectProductName = $("#select_product option:selected").text();
                let addWarranty = $('#add_warranty').val();
                let addStock = $('#add_stock').val();
                let addQuantity = $('#add_quantity').val();
                let addExchangeQuantity = $('#add_exchange_quantity').val();
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

                if ($.inArray(selectProduct, productIds) != -1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: selectProductName + ' already exist in list.',
                    });
                    // Play the notification sound
                    let notificationSound = document.getElementById('notification-error-audio');
                    if (notificationSound) {
                        notificationSound.play();
                    }
                    return false;
                }

                if (selectProduct != '' && addQuantity != '' && addUnitPrice != '' && addSellingUnitPrice !=
                    '') {
                    var addMoreSound = document.getElementById("add_more_sound");
                    addMoreSound.play();
                    var html = $('#template-product').html();
                    var itemHtml = $(html);
                    $('#product-container').prepend(itemHtml);
                    var item = $('.product-item').first();
                    item.hide();
                    item.closest('tr').find('.product_name').text($("#select_product option:selected")
                    .text());
                    item.closest('tr').find('.product_name_val').val($("#select_product option:selected")
                        .text());
                    item.closest('tr').find('.product').val($("#select_product option:selected").val());
                    item.closest('tr').find('.product_code').val($("#select_product option:selected").attr('data-code'));
                    item.closest('tr').find('.warranty').val(addWarranty);
                    item.closest('tr').find('.stock').val(addStock);
                    item.closest('tr').find('.quantity').val(addQuantity);
                    item.closest('tr').find('.exchange_qty').val(addExchangeQuantity);
                    item.closest('tr').find('.unit_price').val(addUnitPrice);
                    item.closest('tr').find('.selling_price').val(addSellingUnitPrice);
                    productIds.push(selectProduct);
                    item.show();
                    calculate();
                    $('#select_product').val(null).trigger('change');
                    $('#add_warranty').val('');
                    $('#add_stock').val('');
                    $('#add_quantity').val('');
                    $('#add_unit_price').val('');
                    $('#add_selling_price').val('');
                }
                return false; // prevent the button click from happening
            });

            $('body').on('click', '.btn-remove', function() {
                var product = $(this).closest('tr').find('.product').val();

                var removeItem = document.getElementById("remove_sound");
                removeItem.play();

                $(this).closest('.product-item').remove();
                calculate();
                productIds = $.grep(productIds, function(value) {
                    return value != product;
                });
            });
            $('body').on('keyup', 'input[type="number"],#tva_percentage,#discount_percentage,#discount',
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
            var tva_percentage = parseFloat($('#tva_percentage').val() || 0);
            var discount = parseFloat($('#discount').val() || 0);
            $('.product-item').each(function(i, obj) {

                let quantity = parseFloat($('.quantity:eq(' + i + ')').val());
                quantity = (isNaN(quantity) || quantity < 0) ? 0 : quantity;

                let unit_price = parseFloat($('.unit_price:eq(' + i + ')').val());
                unit_price = (isNaN(unit_price) || unit_price < 0) ? 0 : unit_price;

                let selling_price = parseFloat($('.selling_price:eq(' + i + ')').val());
                selling_price = (isNaN(selling_price) || selling_price < 0) ? 0 : selling_price;


                $('.total-purchase-cost:eq(' + i + ')').text((quantity * unit_price).toFixed(2));
                total_quantity += quantity;
                total_selling_price += quantity * selling_price;
                total_purchase_price += quantity * unit_price;
            });

            $('#total_quantity').text(total_quantity);
            $('#total_selling_unit_price').text(total_selling_price.toFixed(2));
            $('#total_purchase_price').text(total_purchase_price.toFixed(2));

            if ($('#discount_percentage').val() > 0) {
                discount = ($('#discount_percentage').val() * total_purchase_price) / 100;
                $('#discount').val(discount);

            } else {
                $('#discount_percentage').val(0);
            }

            var tva = ((total_purchase_price - discount) * tva_percentage) / 100;
            var total = parseFloat(total_purchase_price) + tva - discount;
            $('#tva').html('৳' + tva.toFixed(2));
            $('#total-amount').html('৳' + total.toFixed(2));
            $('#total').val(total.toFixed(2));

            if (rows.length > 0) {
                $("#footer_area").show();
            } else {
                $("#footer_area").hide();
            }
        }
    </script>
@endsection
