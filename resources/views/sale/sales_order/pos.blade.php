@extends('layouts.app')
@section('title','Point of Sale')
@section('style')
    <style>
        .input-group.search-bar {
            position: relative;
        }

        .input-group {
            position: relative;
        }

        .search-icon {
            position: absolute;
            right: 7px;
            bottom: 3px;
            color: #7122f3;
            font-size: 19px;
            z-index: 9;
        }

        @media (min-width: 768px) {
            .product-card-body {
                max-height: calc(100vh - 195px);
                min-height: calc(100vh - 195px);
            }

            .customer-card-body {
                max-height: calc(100vh - 175px);
                min-height: calc(100vh - 175px);
            }

            body {
                overflow: hidden !important;
            }
        }

        @media only screen and (max-width: 767px) {
            body {
                overflow: auto !important;
            }
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Products Information</div>
                        </div>
                        <div class="card-header">
                            <div class="input-group search-bar">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                </div>
                                <input type="text" class="form-control" id="barcode"
                                       placeholder="Search for product barcode...">
                                <div class="search-icon" id="barcode-search">
                                    <i id="search-icon-change" class="fa fa-search"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body product-card-body"
                             style="position: relative;padding-right: 6px;scrollbar-width: thin;overflow-y: auto !important;overflow-x: hidden !important;">
                            <form
                                action="{{ $saleOrder->id ? route('sales.update',['sale'=>$saleOrder->id]) : route('sales.store') }}"
                                method="post" id="form-sales">
                                @csrf
                                @if($saleOrder->id)
                                    @method('PUT');
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th class="text-center">S/L</th>
                                            <th class="text-center">Product</th>
                                            <th class="text-center">Barcode</th>
                                            <th class="text-center">Unit</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Unit Price</th>
                                            <th class="text-center"></th>
                                        </tr>
                                        </thead>
                                        <tbody id="product-container">
                                        @foreach($saleOrder->saleOrderItems as $saleOrderItem)
                                            <tr class="product-item">
                                                <td class="text-center sl_text">{{ $loop->iteration }}</td>
                                                <td class="text-center product_name_text">{{ $saleOrderItem->product->name ?? '' }}</td>
                                                <td class="text-center">
                                                    <input type="hidden" value="{{ $saleOrderItem->product_id }}"
                                                           name="product_id[]" class="product_id">
                                                    <input type="hidden" value="{{ $saleOrderItem->inventory_id }}"
                                                           name="inventory_id[]" class="inventory_id">
                                                    <input type="hidden"
                                                           value="{{ $saleOrderItem->product->name ?? '' }}"
                                                           name="product_name[]" class="product_name">
                                                    <input type="hidden" value="{{ $saleOrderItem->product_code }}"
                                                           name="product_code[]" class="product_code">
                                                    <input type="hidden"
                                                           value="{{ $saleOrderItem->product->unit->name ?? '' }}"
                                                           name="product_unit[]" class="product_unit">
                                                    <input type="hidden" value="{{ $saleOrderItem->quantity }}"
                                                           name="product_quantity[]" class="product_quantity">
                                                    <input type="hidden"
                                                           value="{{ $saleOrderItem->selling_unit_price }}"
                                                           name="product_unit_price[]" class="product_unit_price">
                                                    <span
                                                        class="product_code_text">{{ $saleOrderItem->product_code }}</span>
                                                </td>
                                                <td class="text-center product_unit_text">{{ $saleOrderItem->product->unit->name ?? '' }}</td>
                                                <td class="text-center product_quantity_text">{{ $saleOrderItem->quantity }}</td>
                                                <td class="text-right product_unit_price_text">{{ $saleOrderItem->selling_unit_price }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-danger bg-gradient-danger btn-sm btn-remove">
                                                        <i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th class="text-center" colspan="4">Total</th>
                                            <th class="text-center" id="total-quantity"></th>
                                            <th class="text-right" id="total-price"></th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @include('layouts.partial.__inside_preloader')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Billing Information</div>
                <div class="card-body">
                    <div class="customer-card-body" style="padding-right: 10px;scrollbar-width: thin;overflow-x: auto;">

                        <div class="form-group">
                            <label for="seller">Seller <span class="text-danger">*</span></label>
                            <select required style="max-width: max-content !important;" name="seller"
                                    class="form-control select2" id="seller">
                                <option value="">Select Seller</option>
                                @foreach($sellers as $seller)
                                    <option
                                        {{ ($saleOrder ? ($saleOrder->seller_id == $seller->id ? 'selected' : '')  : '') }} value="{{ $seller->id }}">{{ $seller->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="customer">Search Customer <span class="text-danger">*</span>
                                <button type="button" id="add-new-customer"
                                        class="btn btn-primary bg-gradient-indigo btn-sm"><i class="fa fa-plus"></i>
                                </button>
                            </label>
                            <select style="max-width: max-content !important;" name="customer"
                                    class="form-control select2" id="customer">
                                <option value="">Select Customer</option>
                                @if($saleOrder->customer)
                                    <option selected value="{{ $saleOrder->customer_id }}">
                                        {{ $saleOrder->customer->name ?? '' }}
                                        @if($saleOrder->customer->mobile_no != '')
                                            - Mobile No. {{ $saleOrder->customer->mobile_no }}
                                        @endif
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="referral_code">Referral Code</label>
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" {{ ($saleOrder->referral_code ?? false) ? 'readonly' : '' }} value="{{ $saleOrder->referral_code ?? '' }}" autocomplete="off"
                                           placeholder="Enter Referral Code" name="referral_code" id="referral_code"
                                           class="form-control">
                                    <span id="referral_badge"
                                          style="display:{{ ($saleOrder && $saleOrder->referral_code != '') ? 'inline' : 'none' }}"
                                          class="badge badge-success">
                                        @if($saleOrder && $saleOrder->referral_code != '')
                                            <b>Discount: {{ $saleOrder->discount_percent }}%</b>
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="referral_apply" style="{{ ($saleOrder->referral_code ?? false) ? 'display: none' : 'display: inline' }}" class="btn btn-primary bg-gradient-primary btn-sm">Apply</button>
                                    <button type="button" id="referral_remove" style="{{ ($saleOrder->referral_code ?? false) ? 'display: inline' : 'display: none' }}" class="btn btn-danger bg-gradient-danger btn-sm"><i class="fa fa-times"></i></button>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="coupon_code">Coupon Code</label>
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" {{ ($saleOrder->coupon_code ?? false) ? 'readonly' : '' }}  value="{{ $saleOrder->coupon_code ?? '' }}" autocomplete="off"
                                           placeholder="Enter Coupon Code" name="coupon_code" id="coupon_code"
                                           class="form-control">
                                    <span id="coupon_badge"
                                          style="display:{{ ($saleOrder && $saleOrder->coupon_code != '') ? 'inline' : 'none' }}"
                                          class="badge badge-success">
                                        @if($saleOrder && $saleOrder->coupon_code != '')
                                            <b>Discount: {{ $saleOrder->discount_percent }}%</b>
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="coupon_apply" style="{{ ($saleOrder->coupon_code ?? false) ? 'display: none' : 'display: inline' }}" class="btn btn-primary bg-gradient-primary btn-sm">Apply</button>
                                    <button type="button" id="coupon_remove" style="{{ ($saleOrder->coupon_code ?? false) ? 'display: inline' : 'display: none' }}" class="btn btn-danger bg-gradient-danger btn-sm"><i class="fa fa-times"></i></button>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Total: <span id="total-checkout-price"></span></h5>
                        </div>
                        <div class="payment-buttons">
                            <button disabled type="button" class="btn btn-primary bg-gradient-primary btn-block"
                                    id="btn-checkout">
                                Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Select Payment Method</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <span class="text-danger error-message" id="customer-error"></span>
                        <span class="text-danger error-message" id="seller-error"></span>
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="text" autocomplete="off" value="{{ date('d-m-Y') }}" readonly
                                   class="form-control date-picker" placeholder="Enter Date" id="date" name="date">
                            <span class="text-danger error-message" id="date-error"></span>
                            <input type="hidden" name="employee" value="{{ $saleOrder->employee_id ?? '' }}"
                                   id="employee">
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select class="form-control select2" name="payment_method" id="payment_method">
                                <option value="">Select Payment Method</option>
                                @foreach($paymentModes as $paymentMode)
                                    <option value="{{ $paymentMode->id }}">{{ $paymentMode->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-message" id="payment_method-error"></span>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sub_total">Sub Total</label>
                                    <input type="text" class="form-control" name="sub_total" id="sub_total" readonly>
                                    <span class="text-danger error-message" id="sub_total-error"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discount_percent">Discount(%)</label>
                                    <input type="number" step="any" value="{{ $saleOrder->discount_percent ?? '' }}"
                                           class="form-control" id="discount_percent">
                                    <span class="text-danger error-message" id="discount_percent-error"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vat_percent">Vat(%)</label>
                                    <input type="number" value="{{ $saleOrder->vat_percent ?? 7.5 }}" step="any"
                                           name="vat_percent" class="form-control" id="vat_percent">
                                    <span class="text-danger error-message" id="vat_percent-error"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vat_amount">Vat Amount</label>
                                    <input type="text" class="form-control" name="vat_amount" id="vat_amount" readonly>
                                    <span class="text-danger error-message" id="vat_amount-error"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total_amount">Total</label>
                                    <input type="text" class="form-control" name="total_amount" id="total_amount"
                                           readonly>
                                    <span class="text-danger error-message" id="total_amount-error"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paid_amount">Paid</label>
                                    <input type="number" step="any" class="form-control" id="paid_amount">
                                    <span class="text-danger error-message" id="paid_amount-error"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="due_amount">Due</label>
                                    <input type="text" class="form-control" id="due_amount" readonly>
                                    <span class="text-danger error-message" id="due_amount-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" placeholder="Enter notes" id="notes" name="notes"></textarea>
                            <span class="text-danger error-message" id="notes-error"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger bg-gradient-danger" data-dismiss="modal">Close</button>
                    <button type="button" id="submit-payment" class="btn btn-primary bg-gradient-indigo">Submit
                        Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-customer" data-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Customer</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-customer-form" action="{{ route('customer.store-direct') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" id="customer_name" placeholder="Enter Customer Name" class="form-control"
                                   name="customer_name">
                            <span class="text-danger error-message" id="customer_name-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="mobile_no">Mobile No.</label>
                            <input type="text" id="mobile_no" placeholder="Enter Mobile No." class="form-control"
                                   name="mobile_no">
                            <span class="text-danger error-message" id="mobile_no-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" placeholder="Enter Address" class="form-control"
                                   name="address">
                            <span class="text-danger error-message" id="address-error"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger bg-gradient-danger" data-dismiss="modal">Close</button>
                    <button type="button" id="btn-save-customer" class="btn btn-primary bg-gradient-indigo">Save
                    </button>
                </div>
            </div>
        </div>
    </div>
    <template id="template-product">
        <tr class="product-item">
            <td class="text-center sl_text"></td>
            <td class="text-center product_name_text"></td>
            <td class="text-center">
                <input type="hidden" name="product_id[]" class="product_id">
                <input type="hidden" name="inventory_id[]" class="inventory_id">
                <input type="hidden" name="product_name[]" class="product_name">
                <input type="hidden" name="product_code[]" class="product_code">
                <input type="hidden" name="product_unit[]" class="product_unit">
                <input type="hidden" name="product_quantity[]" class="product_quantity">
                <input type="hidden" name="product_unit_price[]" class="product_unit_price">
                <span class="product_code_text"></span>
            </td>
            <td class="text-center product_unit_text"></td>
            <td class="text-center product_quantity_text"></td>
            <td class="text-right product_unit_price_text"></td>
            <td class="text-center">
                <button class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
    </template>
@endsection
@section('script')

    <script>
        var productCodes = [];

        $(function () {
            calculate();
            barcodeAutoComplete('barcode', "{{ route('search-barcode-autocomplete') }}");
            barcodeAutoComplete('coupon_code', "{{ route('search-coupon-code-autocomplete') }}");

            $(".product_code").each(function (index) {
                if ($(this).val() != '') {
                    productCodes.push($(this).val());
                }
            });

            $('body').on('keypress', '#referral_code', function (e) {
                if (e.keyCode == 13) {
                    applyReferralCode();
                    calculatePaid();
                    return false;
                }
            });
            $('body').on('click', '#referral_apply', function () {
                applyReferralCode();
                calculatePaid();
            });
            $('body').on('click', '#referral_remove', function () {

                calculatePaid();
            });

            $('body').on('keypress', '#coupon_code', function (e) {
                if (e.keyCode == 13) {
                    applyCouponCode();
                    calculatePaid();
                    return false;
                }
            });
            $('body').on('click', '#coupon_apply', function () {
                applyCouponCode();
                calculatePaid();
            });
            $('body').on('click', '#coupon_remove', function () {
                couponCodeCancel();
                calculatePaid();
            });

            $('body').on('keypress', '#barcode', function (e) {
                if (e.keyCode == 13) {
                    let productCode = $(this).val();
                    appendProduct(productCode);
                    return false; // prevent the button click from happening
                }
            });
            $("#barcode-search").click(function () {
                let productCode = $("#barcode").val()
                appendProduct(productCode);
            })

            $('body').on('click', '.btn-remove', function () {
                if (confirm('Are you sure?')) {
                    let product_code = $(this).closest('li').find('.product_code').val();
                    let removeItem = document.getElementById("remove_sound");
                    removeItem.play();
                    $(this).closest('.product-item').remove();
                    calculate();
                    productCodes = $.grep(productCodes, function (value) {
                        return value != product_code;
                    });
                }

            });

            $('#customer').select2({
                ajax: {
                    url: "{{ route('party_json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            type: [2],
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $("#add-new-customer").click(function () {
                preloaderToggle(true);
                $("#modal-customer").modal('show');
                preloaderToggle(false);
            })
            $("#btn-save-customer").click(function () {
                $('.error-message').text('');
                preloaderToggle(true);
                $.ajax({
                    type: 'POST',
                    url: $('#modal-customer-form').attr('action'),
                    data: $('#modal-customer-form').serialize(),
                    success: function (response) {
                        preloaderToggle(false);
                        if (response.status) {
                            $("#modal-customer").modal('hide');
                            $('#customer').prepend('<option selected value="' + response.customer.id + '">' + response.customer.customer_info + '</option>');

                            ajaxSuccessMessage(response.message)
                        } else {
                            ajaxErrorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        preloaderToggle(false);
                        // If the form submission encounters an error
                        // Display validation errors
                        if (xhr.status === 422) {
                            ajaxWarningMessage('Please fill up validate required fields.');
                            let errors = xhr.responseJSON.errors;
                            // Clear previous error messages
                            $('.error-message').text('');

                            // Update error messages for each field
                            $.each(errors, function (field, errorMessage) {
                                $('#' + field + '-error').text(errorMessage[0]);
                            });
                        }
                    }
                })
            })
            $("#btn-checkout").click(function () {
                preloaderToggle(true);
                $("#paymentModal").modal('show');
                calculate();
                preloaderToggle(false);
            })
            $("#submit-payment").click(function () {
                $('.error-message').text('');
                preloaderToggle(true);
                var formData = new FormData($('#form-sales')[0]);
                // Append additional data
                formData.append('date', $("#date").val());
                formData.append('referral_code', $("#referral_code").val());
                formData.append('employee', $("#employee").val());
                formData.append('coupon_code', $("#coupon_code").val());
                formData.append('seller', $("#seller").val());
                formData.append('customer', $("#customer").val());
                formData.append('payment_method', $("#payment_method").val());
                formData.append('sub_total', $("#sub_total").val());
                formData.append('discount_percent', $("#discount_percent").val());
                formData.append('vat_percent', $("#vat_percent").val());
                formData.append('vat_amount', $("#vat_amount").val());
                formData.append('total_amount', $("#total_amount").val());
                formData.append('paid_amount', $("#paid_amount").val());
                formData.append('due_amount', $("#due_amount").val());
                $.ajax({
                    type: 'POST',
                    url: $('#form-sales').attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        preloaderToggle(false);
                        if (response.status) {
                            ajaxSuccessMessage(response.message)
                            location.href = response.redirect_url;
                        } else {
                            ajaxErrorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        preloaderToggle(false);
                        // If the form submission encounters an error
                        // Display validation errors
                        if (xhr.status === 422) {
                            ajaxWarningMessage('Please fill up validate required fields.');
                            let errors = xhr.responseJSON.errors;
                            // Clear previous error messages
                            $('.error-message').text('');

                            // Update error messages for each field
                            $.each(errors, function (field, errorMessage) {
                                $('#' + field + '-error').text(errorMessage[0]);
                            });
                        }
                    }
                })
            })


            $('body').on('keyup', '#paid_amount,#vat_percent,#discount_percent', function () {
                calculatePaid();
            });

        })
        function applyReferralCode() {
            couponCodeCancel();

            let referralCode = $('#referral_code').val();
            $('#referral_remove').hide();
            $('#referral_badge').hide();
            if (referralCode != '') {
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_referral_details') }}",
                    data: {referralCode: referralCode}
                }).done(function (response) {
                    if (response.status) {
                        $("#employee").val(response.employee.id);
                        $("#discount_percent").val(response.employee.referral_discount);
                        $("#referral_badge").html('<b>Discount: ' + (response.employee.referral_discount.toFixed(2)) + '%</b>');
                        $('#referral_badge').show();
                        $('#referral_remove').show();
                        $('#referral_apply').hide();
                        $('#referral_code').attr('readonly',true)
                        ajaxSuccessMessage(response.message);
                    } else {
                        $('#referral_remove').hide();
                        $('#referral_apply').show();
                        $('#referral_code').attr('readonly',false)
                        $('#referral_badge').hide();
                        $("#employee").val('');
                        $("#discount_percent").val('');
                        $("#referral_badge").text('');
                        ajaxErrorMessage(response.message);
                    }
                });
            }else {
                ajaxErrorMessage('Please type referral code !');
            }

        }
        function applyCouponCode() {
            referralCancel();
            let couponCode = $('#coupon_code').val();
            let customerId = $('#customer').val();
            if (customerId == ''){
                ajaxErrorMessage('First, select the customer.');
                return false;
            }
            $('#coupon_remove').hide();
            $('#coupon_badge').hide();
            if (couponCode != '') {
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_coupon_details') }}",
                    data: {couponCode: couponCode,customerId:customerId}
                }).done(function (response) {
                    if (response.status) {
                        $("#employee").val();
                        $("#discount_percent").val(response.coupon.discount_value);
                        $("#coupon_badge").html('<b>Discount: ' + (response.coupon.discount_value) + '%</b>');
                        $('#coupon_badge').show();
                        $('#coupon_remove').show();
                        $('#coupon_apply').hide();
                        $('#coupon_code').attr('readonly',true)
                        ajaxSuccessMessage(response.message);
                    } else {
                        $('#coupon_remove').hide();
                        $('#coupon_apply').show();
                        $('#coupon_code').attr('readonly',false)
                        $('#coupon_badge').hide();
                        $("#employee").val('');
                        $("#discount_percent").val('');
                        $("#coupon_badge").text('');
                        ajaxErrorMessage(response.message);
                    }
                });
            }else {
                ajaxErrorMessage('Please type coupon code !');
            }

        }
        function couponCodeCancel(){
            $('#coupon_code').val('').prop('readonly', false);
            $("#employee").val();
            $("#discount_percent").val('');
            $("#coupon_badge").html('');
            $('#coupon_remove').hide();
            $('#coupon_apply').show();
            $('#coupon_badge').hide();
            calculatePaid();
        }
        function referralCancel(){
            $('#referral_code').val('').prop('readonly', false);
            $("#employee").val();
            $("#discount_percent").val('');
            $("#referral_badge").html('');
            $('#referral_remove').hide();
            $('#referral_apply').show();
            $('#referral_badge').hide();
            calculatePaid();
        }

        function appendProduct(productCode) {
            if (productCode == '') {
                ajaxWarningMessage('Please, Enter Barcode !');
                return false;
            }
            if ($.inArray(productCode, productCodes) != -1) {
                ajaxWarningMessage('Already exist in list: ' + productCode);
                return false;
            }

            if (productCode != '') {
                insidePreloaderToggle(true);
                $.ajax({
                    method: "GET",
                    url: "{{ route('pos_product_search') }}",
                    data: {productCode: productCode}
                }).done(function (response) {
                    if (response.status) {
                        let addMoreSound = document.getElementById("add_more_sound");
                        addMoreSound.play();
                        let html = $('#template-product').html();
                        let itemHtml = $(html);
                        $('#product-container').append(itemHtml);
                        let item = $('.product-item').last();
                        item.hide();
                        item.closest('tr').find('.product_name_text').text(response.inventory.product.name);
                        item.closest('tr').find('.product_name').val(response.inventory.product.name);
                        item.closest('tr').find('.product_code_text').text(response.inventory.product_code);
                        item.closest('tr').find('.product_code').val(response.inventory.product_code);
                        item.closest('tr').find('.product_unit_text').text(response.inventory.product.unit.name);
                        item.closest('tr').find('.product_unit').val(response.inventory.product.unit.name);
                        item.closest('tr').find('.product_quantity_text').text(1);
                        item.closest('tr').find('.product_quantity').val(1);
                        item.closest('tr').find('.product_unit_price_text').text(response.inventory.selling_price);
                        item.closest('tr').find('.product_unit_price').val(response.inventory.selling_price);
                        item.closest('tr').find('.product_id').val(response.inventory.product_id);
                        item.closest('tr').find('.inventory_id').val(response.inventory.id);

                        productCodes.push(productCode);
                        item.show();
                        calculate();
                        $('#barcode').val('');
                        insidePreloaderToggle(false);
                    } else if (response.status == false) {
                        $('#barcode').val('');
                        ajaxErrorMessage(response.message);
                        insidePreloaderToggle(false);
                    }
                });
            }
        }

        function calculate() {
            let sl_text = 1;
            let rows = $("#product-container .product-item");
            rows.each(function () {
                var productSlElement = $(this).find('.sl_text');
                productSlElement.text(sl_text);
                sl_text++;
            });


            var total_price = 0;
            var sub_total = 0;
            var total_quantity = 0;
            $('.product-item').each(function (i, obj) {

                let product_qty = parseFloat($('.product_quantity:eq(' + i + ')').val());
                product_qty = (isNaN(product_qty) || product_qty < 0) ? 0 : product_qty;

                let product_unit_price = parseFloat($('.product_unit_price:eq(' + i + ')').val());
                product_unit_price = (isNaN(product_unit_price) || product_unit_price < 0) ? 0 : product_unit_price;

                total_quantity += product_qty;
                sub_total += (product_qty * product_unit_price);
            });

            $('#total-quantity').text(total_quantity);
            $('#sub_total').val(sub_total.toFixed(2));
            $('#total-checkout-price').text(sub_total.toFixed(2));
            calculatePaid();
            if (sub_total > 0) {
                $('#btn-checkout').attr('disabled', false);
            } else {
                $('#btn-checkout').attr('disabled', true);
            }
        }

        function calculatePaid() {

            let vat_percent = parseFloat($('#vat_percent').val());
            vat_percent = (isNaN(vat_percent) || vat_percent < 0) ? 0 : vat_percent;

            let sub_total = parseFloat($('#sub_total').val());
            sub_total = (isNaN(sub_total) || sub_total < 0) ? 0 : sub_total;

            let discount_percent = parseFloat($('#discount_percent').val());
            discount_percent = (isNaN(discount_percent) || discount_percent < 0) ? 0 : discount_percent;

            let discountAmount = (sub_total / 100) * discount_percent;

            let paid_amount = parseFloat($('#paid_amount').val());
            paid_amount = (isNaN(paid_amount) || paid_amount < 0) ? 0 : paid_amount;

            let vatBaseAmount = sub_total;

            let vatAmount = (vatBaseAmount / 100) * vat_percent;
            $('#vat_amount').val(Math.ceil(vatAmount).toFixed(2));

            let total_amount = (sub_total - discountAmount) + vatAmount;
            $('#total_amount').val(Math.ceil(total_amount).toFixed(2));

            let due_amount = (total_amount) - paid_amount;
            $('#due_amount').val(Math.ceil(due_amount).toFixed(2));
        }

    </script>
@endsection
