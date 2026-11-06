@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Sales Order Edit
@endsection

@section('content')
    <form method="POST" enctype="multipart/form-data" action="{{ route('sale_receipt.edit', ['order' => $order->id]) }}">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Order Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('department_id') ? 'has-error' :'' }}">
                                    <label> Department *</label>

                                    <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id" data-placeholder="Select Department" required>
                                        <option value="">Select Department</option>

                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $order->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('department_id')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('customer') ? 'has-error' :'' }}">
                                    <label>Customer</label>

                                    <select class="form-control select2" style="width: 100%;" name="customer">
                                        <option value="">Select Customer</option>

                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ empty(old('customer')) ? ($errors->has('customer') ? '' : ($order->customer_id == $customer->id ? 'selected' : '')) :
                                            (old('customer') == $customer->id ? 'selected' : '') }}>{{ $customer->name.' - '.$customer->mobile_no.' - '.$customer->address }}</option>
                                        @endforeach
                                    </select>

                                    @error('customer')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('Y-m-d',strtotime($order->date))) : old('date') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('received_by') ? 'has-error' :'' }}">
                                    <label>Order By</label>

                                    <input class="form-control" type="text" name="received_by" value="{{ empty(old('received_by')) ? ($errors->has('received_by') ? '' : $order->received_by) : old('received_by') }}">

                                    @error('received_by')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Products</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%"> Exchange </th>
                                    <th width="15%">Unit Price</th>
                                    <th>Total Cost</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="product-container">
                                @if (old('serial') != null && sizeof(old('serial')) > 0)
                                    @foreach(old('serial') as $item)
                                        <tr class="product-item">
                                            <td>
                                                <div class="form-group {{ $errors->has('serial.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="search" class="form-control serial" name="serial[]" value="{{ old('serial.'.$loop->index) }}" autocomplete="off">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('product_name.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control product_name" name="product_name[]" value="{{ old('product_name.'.$loop->index) }}" readonly>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('exchange_qty.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control exchange_qty" name="exchange_qty[]" value="{{ old('exchange_qty.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td class="total-cost">৳0.00</td>
                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach($order->products as $product)
                                        <tr class="product-item">
                                            <td>
                                                <input type="search" class="form-control serial" name="serial[]" autocomplete="off" value="{{ $product->pivot->serial }}">
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control product_name" name="product_name[]" readonly value="{{ $product->pivot->name }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control quantity" name="quantity[]" value="{{ $product->pivot->quantity }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control exchange_qty" name="exchange_qty[]" value="{{ $product->pivot->exchange_qty }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control unit_price" name="unit_price[]" value="{{ $product->pivot->unit_price }}">
                                                </div>
                                            </td>

                                            <td class="total-cost">৳0.00</td>
                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>

                        <a role="button" class="btn btn-info btn-sm" id="btn-add-product" style="margin-bottom: 10px">Add Product</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Services</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th width="55%">Name</th>
                                    <th width="10%">Quantity</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="20%">Total Cost</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="service-container">
                                @if (old('service_name') != null && sizeof(old('service_name')) > 0)
                                    @foreach(old('service_name') as $item)
                                        <tr class="service-item">
                                            <td>
                                                <div class="form-group {{ $errors->has('service_name.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control service_name" name="service_name[]" value="{{ old('service_name.'.$loop->index) }}" autocomplete="off">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('service_quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" step="any" class="form-control service_quantity" name="service_quantity[]" value="{{ old('service_quantity.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('service_unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control service_unit_price" name="service_unit_price[]" value="{{ old('service_unit_price.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td class="service-total-cost">৳0.00</td>
                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove-service">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach($order->services as $service)
                                        <tr class="service-item">
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control service_name" name="service_name[]" autocomplete="off" value="{{ $service->name }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <input type="number" step="any" class="form-control service_quantity" name="service_quantity[]" value="{{ $service->quantity }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group">
                                                    <input type="text" class="form-control service_unit_price" name="service_unit_price[]" value="{{ $service->unit_price }}">
                                                </div>
                                            </td>

                                            <td class="service-total-cost">৳0.00</td>
                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove-service">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>

                        <a role="button" class="btn btn-info btn-sm" id="btn-add-service" style="margin-bottom: 10px">Add Service</a>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Summary</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 col-md-offset-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th colspan="4" class="text-right">Product Sub Total</th>
                                        <th id="product-sub-total">৳0.00</th>
                                    </tr>
                                    {{-- <tr>
                                        <th colspan="4" class="text-right">Service Sub Total</th>
                                        <th id="service-sub-total">৳0.00</th>
                                    </tr> --}}
                                    <tr>
                                        <th colspan="4" class="text-right">Product TVA (%)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('vat') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="vat" id="vat" value="{{ empty(old('vat')) ? ($errors->has('vat') ? '' : $order->vat_percentage) : old('vat') }}">
                                                <span id="vat_total">৳0.00</span>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <th colspan="4" class="text-right">Service VAT (%)</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('service_vat') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="service_vat" id="service_vat" value="{{ empty(old('service_vat')) ? ($errors->has('service_vat') ? '' : $order->service_vat_percentage) : old('service_vat') }}">
                                                <span id="service_vat_total">৳0.00</span>
                                            </div>
                                        </td>
                                    </tr> --}}
                                    <tr>
                                        <th colspan="4" class="text-right">Product Discount</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="discount" id="discount" value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : $order->discount) : old('discount') }}">
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <th colspan="4" class="text-right">Service Discount</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('service_discount') ? 'has-error' :'' }}">
                                                <input type="text" class="form-control" name="service_discount" id="service_discount" value="{{ empty(old('service_discount')) ? ($errors->has('service_discount') ? '' : $order->service_discount) : old('service_discount') }}">
                                            </div>
                                        </td>
                                    </tr> --}}
                                    <tr>
                                        <th colspan="4" class="text-right">Total</th>
                                        <th id="final-amount">৳0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">Paid</th>
                                        <th id="paid">৳{{ number_format($order->paid,2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">Due</th>
                                        <th id="due">৳0.00</th>
                                    </tr>
                                    {{-- <tr>
                                        <th colspan="4" class="text-right">Refund</th>
                                        <th id="refund_view">৳0.00</th>
                                    </tr> --}}
                                    <tr id="tr-next-payment">
                                        <th colspan="4" class="text-right">Next Payment Date</th>
                                        <td>
                                            <div class="form-group {{ $errors->has('next_payment') ? 'has-error' :'' }}">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right" id="next_payment" name="next_payment" value="{{ empty(old('next_payment')) ? ($errors->has('next_payment') ? '' : ($order->next_payment ? $order->next_payment->format('Y-m-d') : '')) : old('next_payment') }}" autocomplete="off">
                                                </div>
                                                <!-- /.input group -->
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <input type="hidden" name="total" id="total">
                        <input type="hidden" name="due_total" id="due_total">
                        <input type="hidden" name="refund" id="refund">
                        <input type="hidden" name="service_vat" value="0">
                        <input type="hidden" name="service_discount" value="0">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <template id="template-product">
        <tr class="product-item">
            <td>
                <input type="search" class="form-control serial" name="serial[]" autocomplete="off">
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control product_name" name="product_name[]" readonly>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control exchange_qty" name="exchange_qty[]" value="0">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" name="unit_price[]">
                </div>
            </td>

            <td class="total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>

    <template id="template-service">
        <tr class="service-item">
            <td>
                <div class="form-group">
                    <input type="text" class="form-control service_name" name="service_name[]" autocomplete="off">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control service_quantity" name="service_quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control service_unit_price" name="service_unit_price[]">
                </div>
            </td>

            <td class="service-total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove-service">X</a>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Date picker
            $('#date, #next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('.serial').autocomplete({
                source:function (request, response) {
                    $.getJSON('{{ route('get_serial_suggestion') }}?term='+request.term, function (data) {
                        var array = $.map(data, function (row) {
                            return {
                                value: row.code,
                                label: row.code
                            }
                        });

                        response($.ui.autocomplete.filter(array, request.term));
                    })
                },
                minLength: 3,
                delay: 500,
            });

            var message = '{{ session('message') }}';

            if (!window.performance || window.performance.navigation.type != window.performance.navigation.TYPE_BACK_FORWARD) {
                if (message != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message,
                    });
                }
            }

            var serials = [];

            $( ".serial" ).each(function( index ) {
                if ($(this).val() != '') {
                    serials.push($(this).val());
                }
            });

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                item.find('.serial').autocomplete({
                    source:function (request, response) {
                        $.getJSON('{{ route('get_serial_suggestion') }}?term='+request.term, function (data) {
                            var array = $.map(data, function (row) {
                                return {
                                    value: row.code,
                                    label: row.code
                                }
                            });

                            response($.ui.autocomplete.filter(array, request.term));
                        })
                    },
                    minLength: 3,
                    delay: 500,
                });

                $('#product-container').append(item);

                /*if ($('.product-item').length + $('.service-item').length >= 1 ) {
                    $('.btn-remove').show();
                    $('.btn-remove-service').show();
                }*/
            });

            $('body').on('click', '.btn-remove', function () {
                var serial = $(this).closest('tr').find('.serial').val();
                $(this).closest('.product-item').remove();
                calculate();

                /*if ($('.product-item').length + $('.service-item').length <= 1 ) {
                    $('.btn-remove').hide();
                    $('.btn-remove-service').hide();
                }*/

                serials = $.grep(serials, function(value) {
                    return value != serial;
                });
            });

            $('body').on('keyup', '.quantity, .unit_price, .service_quantity, .service_unit_price, #vat, #service_vat, #discount, #service_discount, #paid', function () {
                calculate();
            });

            $('body').on('change', '.quantity, .unit_price, .service_quantity, .service_unit_price', function () {
                calculate();
            });

            /*if ($('.product-item').length + $('.service-item').length <= 1 ) {
                $('.btn-remove').hide();
                $('.btn-remove-service').hide();
            } else {
                $('.btn-remove').show();
                $('.btn-remove-service').show();
            }*/

            calculate();

            $('body').on('keypress', '.serial', function (e) {
                if (e.keyCode == 13) {
                    var departmentId = $('#department_id').val();
                    var serial = $(this).val();
                    $this = $(this);

                    if($.inArray(serial, serials) != -1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Already exist in list.',
                        });

                        return false;
                    }
                    if (departmentId == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Select Department.',
                        });
                    } else {
                        $.ajax({
                            method: "GET",
                            url: "{{ route('sale_product.details') }}",
                            data: { departmentId: departmentId,serial: serial }
                        }).done(function( response ) {
                            // console.log(response);
                            if (response.success) {
                                $this.closest('tr').find('.product_name').val(response.data.product.name);
                                $this.closest('tr').find('.quantity').val(1);
                                $this.closest('tr').find('.quantity').attr({
                                    "max" : response.count,
                                    "min" : 1
                                });
                                $this.closest('tr').find('.unit_price').val(response.data.selling_price);
                                $this.closest('tr').find('.buying_price').val(response.data.including_price);
                                serials.push(response.data.serial_no);
                                calculate();
                            } else {
                                $this.closest('tr').find('.product_name').val('');
                                $this.closest('tr').find('.quantity').val('');
                                $this.closest('tr').find('.unit_price').val('');
                                $this.closest('tr').find('.buying_price').val('');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'This product is not available',
                                });
                                calculate();
                            }
                        });
                    }
                    return false; // prevent the button click from happening
                }
            });

            // Service
            $('#btn-add-service').click(function () {
                var html = $('#template-service').html();
                var item = $(html);

                $('#service-container').append(item);

                if ($('.product-item').length + $('.service-item').length >= 1 ) {
                    $('.btn-remove').show();
                    $('.btn-remove-service').show();
                }
            });

            $('body').on('click', '.btn-remove-service', function () {
                $(this).closest('.service-item').remove();
                calculate();

                if ($('.product-item').length + $('.service-item').length <= 1 ) {
                    $('.btn-remove').hide();
                    $('.btn-remove-service').hide();
                }
            });
        });

        function calculate() {
            var productSubTotal = 0;
            var serviceSubTotal = 0;

            var vat = $('#vat').val();
            var discount = $('#discount').val();
            var serviceVat = $('#service_vat').val();
            var serviceDiscount = $('#service_discount').val();
            var paid = parseFloat('{{ $order->paid }}').toFixed(2);
            var refund = 0;

            if (vat == '' || vat < 0 || !$.isNumeric(vat))
                vat = 0;

            if (discount == '' || discount < 0 || !$.isNumeric(discount))
                discount = 0;

            if (serviceVat == '' || serviceVat < 0 || !$.isNumeric(serviceVat))
                serviceVat = 0;

            if (serviceDiscount == '' || serviceDiscount < 0 || !$.isNumeric(serviceDiscount))
                serviceDiscount = 0;

            $('.product-item').each(function(i, obj) {
                var quantity = $('.quantity:eq('+i+')').val();
                var unit_price = $('.unit_price:eq('+i+')').val();


                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;

                $('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                productSubTotal += quantity * unit_price;
            });

            $('.service-item').each(function(i, obj) {
                var quantity = $('.service_quantity:eq('+i+')').val();
                var unit_price = $('.service_unit_price:eq('+i+')').val();


                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;

                $('.service-total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                serviceSubTotal += quantity * unit_price;
            });


            var productTotalVat = (productSubTotal * vat) / 100;
            var serviceTotalVat = (serviceSubTotal * serviceVat) / 100;


            $('#product-sub-total').html('৳' + productSubTotal.toFixed(2));
            $('#service-sub-total').html('৳' + serviceSubTotal.toFixed(2));

            $('#vat_total').html('৳' + productTotalVat.toFixed(2));
            $('#service_vat_total').html('৳' + serviceTotalVat.toFixed(2));

            var total = parseFloat(productSubTotal) + parseFloat(serviceSubTotal) +
                parseFloat(productTotalVat) + parseFloat(serviceTotalVat) -
                parseFloat(discount) - parseFloat(serviceDiscount);

            var due = parseFloat(total) - parseFloat(paid);

            if (due < 0) {
                var previousDue = due;
                due -= previousDue;
                refund = paid - total - due;
            }

            $('#final-amount').html('৳' + total.toFixed(2));
            $('#due').html('৳' + due.toFixed(2));
            $('#total').val(total.toFixed(2));
            $('#due_total').val(due.toFixed(2));
            $('#refund').val(refund.toFixed(2));
            $('#refund_view').html('৳' + refund.toFixed(2));

            if (due > 0) {
                $('#tr-next-payment').show();
            } else {
                $('#tr-next-payment').hide();
            }
        }
    </script>
@endsection
