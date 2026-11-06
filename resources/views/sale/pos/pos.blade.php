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
        <div class="col-md-9">
            <form action="{{ route('sales_order.create') }}" method="POST" id="form-sales">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Products Information</div>
                            </div>
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group search-bar">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="barcode"
                                                placeholder="Search for product name or barcode...">
                                            <div class="search-icon" id="barcode-search">
                                                <i id="search-icon-change" class="fa fa-search"></i>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="input-group search-bar">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="barcode"
                                                placeholder="Search for product barcode...">
                                            <div class="search-icon" id="barcode-search1">
                                                <i id="search1-icon-change" class="fa fa-search"></i>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="card-body product-card-body"
                                style="position: relative;padding-right: 6px;scrollbar-width: thin;overflow-y: auto !important;overflow-x: hidden !important;">
                                
                                    <input type="hidden" name="type_of_sale" value="pos">  {{--Sale type for pos--}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th class="text-center">S/L</th>
                                                <th class="text-center">Item Details</th>
                                                <th class="text-center">Batch</th>
                                                <th class="text-center">Stock</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center d-none">Vat(%)</th>
                                                <th class="text-center">Dis(%)</th>
                                                <th class="text-center">Dis Amt</th>
                                                <th class="text-center">Selling Price</th>
                                                <th class="text-center">Total Price</th>
                                                <th class="text-center"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="product-container">
                                            
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th class="text-right" colspan="4">Total</th>
                                                <th class="text-right" id="total-quantity"></th>
                                                <th class="text-right d-none" id="total_vat_amount"></th>
                                                <th></th>
                                                <th class="text-right" id="total_dis_price"></th>
                                                <th></th>
                                                <th class="text-right" id="total_selling_price"></th>
                                                <th></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    @include('layouts.partial.__inside_preloader')
                                
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Billing Information</div>
                <div class="card-body">
                    <div class="customer-card-body" style="padding-right: 10px;scrollbar-width: thin;overflow-x: auto;">
                        <input type="hidden" name="sister_concern_id" id="sister_concern_id" value="{{ old('sister_concern_id', $sisterConcerns->first()->id ?? '') }}">
                        
                        <div class="form-group {{ $errors->has('sale_type') ? 'has-error' : '' }}">
                            <label for="sale_type">Sale Type <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="sale_type" name="sale_type" required>
                                <option value="1"{{ old('sale_type') == 1 ? 'selected' : '' }}>Offline</option>
                            </select>
                            @error('sale_type')
                                <span class="help-block">{{ $message }}</span>
                            @enderror
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
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="received_by">Received By</label>
                            <input type="text" value="{{ old('received_by',auth()->user()->name) }}" name="received_by" id="received_by"
                                class="form-control" placeholder="">
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
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="sub_total">Sub Total</label>
                                    <input type="text" class="form-control" name="sub_total" id="sub_total" readonly>
                                    <span class="text-danger error-message" id="sub_total-error"></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="text" class="form-control" name="total_discount" id="total_discount" readonly>
                                    <span class="text-danger error-message" id="discount-error"></span>
                                </div>
                            </div>
                            <div class="col-md-2 d-none">
                                <div class="form-group">
                                    <label for="total_vat">Vat</label>
                                    <input type="text" class="form-control" name="total_vat" id="total_vat" readonly>
                                    <span class="text-danger error-message" id="total_vat-error"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total_amount">Total</label>
                                    <input type="hidden" class="form-control" name="total" id="total">
                                    <input type="text" class="form-control" name="total_amount" id="total_amount" readonly>
                                    <span class="text-danger error-message" id="total_amount-error"></span>
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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <select class="form-control select2" name="payment_type" id="payment_type">
                                        <option value="">Select Option</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank(Card)</option>
                                        <option value="3">Mobile Banking</option>
                                    </select>
                                    <span class="text-danger error-message" id="payment_type-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4" id="modal_mobile_bank_info">
                                <div class="form-group">
                                    <label>Mobile Banking Account</label>
                                    <select class="form-control select2" id="mobile_banking" name="mobile_banking">
                                        <option value="">Select Account</option>
                                        @foreach ($mobileBankings as $mobileBanking)
                                            <option value="{{ $mobileBanking->id }}">
                                                {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message" id="mobile_banking-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4" id="modal_bank_info">
                                <div class="form-group">
                                    <label> Bank Account</label>
                                    <select class="form-control select2" id="account" name="account">
                                        <option value="">Select Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->account_no }} -
                                                {{ $account->branch->name??'' }} -
                                                {{ $account->bank->name??'' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message" id="account-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paid">Paid</label>
                                    <input type="text" step="any" value="0" class="form-control" id="paid">
                                    <span class="text-danger error-message" id="paid-error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_type_two">Payment Type (Additional)</label>
                                    <select class="form-control select2" name="payment_type_two" id="payment_type_two">
                                        <option value="">Select Option</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank(Card)</option>
                                        <option value="3">Mobile Banking</option>
                                    </select>
                                    <span class="text-danger error-message" id="payment_type_two-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4" id="modal_mobile_bank_info_two">
                                <div class="form-group">
                                    <label>Mobile Banking Account</label>
                                    <select class="form-control select2" id="mobile_banking_two" name="mobile_banking_two">
                                        <option value="">Select Account</option>
                                        @foreach ($mobileBankings as $mobileBanking)
                                            <option value="{{ $mobileBanking->id }}">
                                                {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message" id="mobile_banking_two-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4" id="modal_bank_info_two">
                                <div class="form-group">
                                    <label> Bank Account</label>
                                    <select class="form-control select2" id="account_two" name="account_two">
                                        <option value="">Select Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->account_no }} -
                                                {{ $account->branch->name??'' }} -
                                                {{ $account->bank->name??'' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message" id="account_two-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paid_two">Paid</label>
                                    <input type="text" step="any" value="0" class="form-control" id="paid_two">
                                    <span class="text-danger error-message" id="paid_two-error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_type_three">Payment Type (Additional)</label>
                                    <select class="form-control select2" name="payment_type_three" id="payment_type_three">
                                        <option value="">Select Option</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank(Card)</option>
                                        <option value="3">Mobile Banking</option>
                                    </select>
                                    <span class="text-danger error-message" id="payment_type_three-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4" id="modal_mobile_bank_info_three">
                                <div class="form-group">
                                    <label>Mobile Banking Account</label>
                                    <select class="form-control select2" id="mobile_banking_three" name="mobile_banking_three">
                                        <option value="">Select Account</option>
                                        @foreach ($mobileBankings as $mobileBanking)
                                            <option value="{{ $mobileBanking->id }}">
                                                {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message" id="mobile_banking_three-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4" id="modal_bank_info_three">
                                <div class="form-group">
                                    <label> Bank Account</label>
                                    <select class="form-control select2" id="account_three" name="account_three">
                                        <option value="">Select Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->account_no }} -
                                                {{ $account->branch->name??'' }} -
                                                {{ $account->bank->name??'' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message" id="account_three-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paid_three">Paid</label>
                                    <input type="text" step="any" value="0" class="form-control" id="paid_three">
                                    <span class="text-danger error-message" id="paid_three-error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" placeholder="Enter notes" id="notes" name="notes"></textarea>
                                    <span class="text-danger error-message" id="notes-error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" value="{{ date('d-m-Y') }}" readonly
                                           class="form-control date-picker" placeholder="Enter Date" id="date" name="date">
                                    <span class="text-danger error-message" id="date-error"></span>
                                    <input type="hidden" name="employee" value=""
                                           id="employee">
                                </div>

                            </div>
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

    <!-- Modal for new Customer-->
    <div class="modal fade" id="newCustomerModal" tabindex="-1" aria-labelledby="newCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="min-width: 50% !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="margin-left: 41% !important" id="newCustomerModalLabel">Customer Info
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form style="font-size: 12px;" enctype="multipart/form-data" id="newCustomerForm">
                        <div class="container">
                            <div class="row">
                                <input type="hidden" name="department" value="1">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Customer Name</label>
                                        <div class="input-group name">
                                            <input type="text"
                                                class="form-control new_customer {{ $errors->has('name') ? 'is-invalid' : 'is-valid-border' }}"
                                                name="name" value="{{ old('name') }}" placeholder="Name">
                                        </div>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_no">Mobile No</label>
                                        <div class="input-group mobile_no">
                                            <input type="text"
                                                class="form-control {{ $errors->has('mobile_no') ? 'is-invalid' : 'is-valid-border' }}"
                                                placeholder="Enter Mobile No." name="mobile_no"
                                                value="{{ old('mobile_no') }}">
                                        </div>
                                        @error('mobile_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opening_due">Email</label>
                                        <div class="input-group email">
                                            <input type="text"
                                                class="form-control {{ $errors->has('email') ? 'is-invalid' : 'is-valid-border' }}"
                                                name="email" placeholder="Email">
                                        </div>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <div class="input-group address">
                                            <textarea class="form-control is-valid-border" rows="3" name="address"></textarea>
                                        </div>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-danger bg-gradient-danger btn-sm float-right"
                                data-dismiss="modal">Close</a>
                            <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <template id="template-product">
        <tr class="product-item">
            <input type="hidden" name="product[]" class="product_id">
            {{-- <input type="hidden" name="inventory_id[]" class="inventory_id">
            <input type="hidden" name="product_name[]" class="product_name"> --}}
            <input type="hidden" name="serial[]" class="product_code">
            {{-- <input type="hidden" name="product_unit[]" class="product_unit">
            <input type="hidden" name="quantity[]" class="product_quantity">
            <input type="hidden" name="unit_price[]" class="product_unit_price"> --}}

            <td class="text-center sl_text"></td>
            <td class="text-center product_name_text"></td>
            <td class="text-center">
                <div class="form-group">
                    <input type="number" readonly class="form-control text-right batch" name="batch[]">
                </div>
            </td>
            <td class="text-center">
                <div class="form-group">
                    <input type="number" readonly class="form-control text-right stock" name="stock[]">
                </div>
            </td>
            <td class="text-right">
                <div class="form-group">
                    <input type="number" value="1" class="form-control text-right quantity" name="quantity[]">
                </div>
            </td>
            <td class="text-center d-none">
                <div class="form-group">
                    <input type="number" value="0" class="form-control text-right vat_percent" name="vat_percent[]">
                    <small>Vat amount: <span style="color: #7122f3" class="vat_amount"></span></small>
                </div>
            </td>
            <td class="text-center">
                <div class="form-group">
                    <input type="number" value="0" class="form-control text-right discount" name="discount[]">
                </div>
            </td>
            <td class="text-center">
                <div class="form-group">
                    <input type="number" value="0" class="form-control text-right discount_amt" name="discount_amt[]">
                    <input type="hidden" name="buying_price[]" class="buying_price">
                    <small>Less than or equal to <span style="color: #7122f3" class="maximum_discount"></span></small>
                </div>
            </td>
            <td class="text-right">
                <div class="form-group">
                    <input type="number" readonly class="form-control text-right unit_price" name="unit_price[]">
                </div>
            </td>
            <td class="total-selling-cost"></td>
            <td class="text-center">
                <button class="btn btn-danger bg-gradient-danger btn-sm btn-remove"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
    </template>
@endsection
@section('script')

    <script>
        var productCodes = [];
        var isProcessing = false;
        $(function () {

            $('#add-new-customer').click(function() {
                $('#newCustomerModal').modal('show');
            });
            $('#newCustomerForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#newCustomerForm')[0]);
                $.ajax({
                    method: "Post",
                    url: "{{ route('customer.add_ajax_customer') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {
                    if (response.success) {
                        $('#newCustomerModal').modal('hide');
                        $("#customer").append('<option value="' + response.customer.id +
                            '" selected>' + response.customer.name + '</option>');
                        //$('.customer').trigger('change');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                });
            });


            $('#payment_type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal_bank_info').hide();
                    $('#modal_mobile_bank_info').hide();
                    if($(this).val() == '3'){
                        $('#modal_mobile_bank_info').show();
                    }
                } else if($(this).val() == '2') {
                    $('#modal_bank_info').show();
                    $('#modal_mobile_bank_info').hide();
                }else{
                    $('#modal_bank_info').hide();
                    $('#modal_mobile_bank_info').hide();
                }
            });
            $('#payment_type').trigger('change');

            // Toggle modal bank info section
            $('#payment_type_two').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal_bank_info_two').hide();
                    $('#modal_mobile_bank_info_two').hide();
                    if($(this).val() == '3'){
                        $('#modal_mobile_bank_info_two').show();
                    }
                } else if($(this).val() == '2'){
                    $('#modal_bank_info_two').show();
                    $('#modal_mobile_bank_info_two').hide();
                }else{
                    $('#modal_bank_info_two').hide();
                    $('#modal_mobile_bank_info_two').hide();
                }
            }).trigger('change');

            // Toggle modal bank info section
            $('#payment_type_three').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal_bank_info_three').hide();
                    $('#modal_mobile_bank_info_three').hide();
                    if($(this).val() == '3'){
                        $('#modal_mobile_bank_info_three').show();
                    }
                } else if($(this).val() == '2'){
                    $('#modal_bank_info_three').show();
                    $('#modal_mobile_bank_info_three').hide();
                }else{
                    $('#modal_bank_info_three').hide();
                    $('#modal_mobile_bank_info_three').hide();
                }
            }).trigger('change');

            calculate();
            // barcodeAutoComplete('barcode', "{{ route('search-barcode-autocomplete') }}");
            barcodeAutoComplete('barcode', "{{ route('search-barcode-autocomplete') }}");

            $(".product_code").each(function (index) {
                if ($(this).val() != '') {
                    productCodes.push($(this).val());
                }
            });
            $('body').on('keypress', '#product_barcode', function (e) {
                if (e.keyCode == 13) {
                    // let productCode = $(this).val();
                    let productCode = ($(this).val()).split('|')[1].trim();
                    let batchNo = ($(this).val()).split('|')[2].trim();
                    appendProduct(productCode,batchNo);
                    return false; // prevent the button click from happening
                }
            });
            
            $('body').on('keypress', '#barcode', function (e) {
                if (e.keyCode == 13) {
                    if (isProcessing) return false; 
                    isProcessing = true;

                    const input = $(this).val().trim();
                    let isMachineInput = input.includes('-') && !input.includes('|');
                    let isManualInput = input.includes('|') && input.includes('-');

                    if (isMachineInput) {
                        const productCode = input.split('-')[0].trim() + '-' + input.split('-')[1].trim();
                        const batchNo = input.split('-')[2].trim();

                        appendProduct(productCode, batchNo);
                    } else if (isManualInput) {
                        const parts = input.split('|');
                        const productCode = parts[1].trim(); // FMP-000001
                        const batchNo = parts[2].trim(); // 1

                        appendProduct(productCode, batchNo);
                    } else {
                        console.error('Invalid input format');
                        isProcessing = false;
                    }

                    // isProcessing = false;
                    return false;
                }
            });
            $("#barcode-search").click(function () {
                if (isProcessing) return false; 
                isProcessing = true;
                // let productCode = $("#barcode").val();
                let productCode = ($("#barcode").val()).split('|')[1].trim();
                let batchNo = ($("#barcode").val()).split('|')[2].trim();
                appendProduct(productCode,batchNo);

                // isProcessing = false;
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
                formData.append('sale_type', $("#sale_type").val());
                formData.append('customer', $("#customer").val());
                formData.append('sister_concern_id', $("#sister_concern_id").val());
                formData.append('payment_type', $("#payment_type").val());
                formData.append('payment_type_two', $("#payment_type_two").val());
                formData.append('payment_type_three', $("#payment_type_three").val());
                formData.append('mobile_banking', $("#mobile_banking").val());
                formData.append('mobile_banking_two', $("#mobile_banking_two").val());
                formData.append('mobile_banking_three', $("#mobile_banking_three").val());
                formData.append('account', $("#account").val());
                formData.append('account_two', $("#account_two").val());
                formData.append('account_three', $("#account_three").val());
                formData.append('sub_total', $("#sub_total").val());
                formData.append('total', $("#total").val());
                formData.append('paid', $("#paid").val());
                formData.append('paid_two', $("#paid_two").val());
                formData.append('paid_three', $("#paid_three").val());
                formData.append('due_total', $("#due_amount").val());
                formData.append('received_by', $("#received_by").val());
                formData.append('notes', $("#notes").val());
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


            // $('body').on('keyup', '#paid_amount,#vat_percent,#discount_percent', function () {
            //     calculatePaid();
            // });

            $('body').on('keyup change', 'input[type="number"]:not(.discount_amt,.discount,.quantity), #discount_percentage, #discount',
                function() {
                    calculate();
            });
            // Update calculations on input change
            $('body').on('keyup change', '#paid',
                function() {
                    calculatePaid();
                });
            // Update calculations on input change
            $('body').on('keyup change', '.discount_amt',
                function() {
                    calculateDiscount();
                });
            // Update calculations on input change
            $('body').on('keyup change', '.discount',
                function() {
                    calculateDiscountPercent();
                });
            // Update calculations on input change
            $('body').on('keyup change', '.quantity',
                function() {
                    calculateQuantity();
                });
            // Update calculations on input change
            $('body').on('keyup change', '#paid_two',
                function() {
                    calculatePaidTwo();
                });
            $('body').on('focus','#paid_two',function(){
                var total = $('#total').val() || 0;
                let paid = $('#paid').val() || 0;
                let paid_two = $('#paid_two').val() || 0;
                let paid_three = $('#paid_three').val() || 0;
                let dueAmount = total - paid - paid_two - paid_three;

                $('#paid_two').val(dueAmount);

                paid = $('#paid').val() || 0;
                paid_two = $('#paid_two').val() || 0;
                paid_three = $('#paid_three').val() || 0;
                dueAmount = total - paid - paid_two - paid_three;

                $('#due_total').val(dueAmount.toFixed(2));
                $('#due_amount').val(dueAmount.toFixed(2));
            });
            // Update calculations on input change
            $('body').on('keyup change', '#paid_three',
                function() {
                    calculatePaidThree();
                });

            $('body').on('focus','#paid_three',function(){
                var total = $('#total').val() || 0;
                let paid = $('#paid').val() || 0;
                let paid_two = $('#paid_two').val() || 0;
                let paid_three = $('#paid_three').val() || 0;
                let dueAmount = total - paid - paid_two - paid_three;

                $('#paid_three').val(dueAmount);

                paid = $('#paid').val() || 0;
                paid_two = $('#paid_two').val() || 0;
                paid_three = $('#paid_three').val() || 0;
                dueAmount = total - paid - paid_two - paid_three;

                $('#due_total').val(dueAmount.toFixed(2));
                $('#due_amount').val(dueAmount.toFixed(2));
            });
        })

        function appendProduct(productCode,batchNo) {
            if (productCode == '') {
                ajaxWarningMessage('Please, Enter Barcode !');
                isProcessing = false;
                return false;
            }
            if (batchNo == '') {
                ajaxWarningMessage('Please, Enter batch No !');
                isProcessing = false;
                return false;
            }
              // Check for duplicates
              let isDuplicate = false;
            $("#product-container .product-item").each(function() {
                let existingProduct = $(this).find('.product_code').val();
                let existingBatch = $(this).find('.batch').val();

                // if (existingProduct === productCode && existingBatch === batchNo) {
                //     isDuplicate = true;
                //     return false; // Exit loop
                // }
                if (existingProduct === productCode) {
                    isDuplicate = true;
                    return false; // Exit loop
                }
            });

            if (isDuplicate) {
                ajaxWarningMessage('Duplicate Entry!This product and batch are already added!');
                $('#barcode').val('');
                isProcessing = false;
                return false;
            }
            if (productCode != '' && batchNo != '') {
                insidePreloaderToggle(true);
                $.ajax({
                    method: "GET",
                    url: "{{ route('pos_product_search') }}",
                    data: {productCode: productCode,batchNo: batchNo}
                }).done(function (response) {
                    if (response.status) {
                        console.log(response);
                        let addMoreSound = document.getElementById("add_more_sound");
                        addMoreSound.play();
                        let html = $('#template-product').html();
                        let itemHtml = $(html);
                        $('#product-container').prepend(itemHtml);
                        let item = $('.product-item').first();
                        item.hide();
                        var batchId = response.inventory.batch_no;
                        var productId = response.inventory.product_id;
                        var sisterConcernId = response.inventory.sister_concern_id;

                        if (batchId) {
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_product_inventory_batch_wise') }}",
                                data: {
                                    id: batchId,
                                    product_id: productId,
                                    sister_concern_id: sisterConcernId
                                },
                            }).done(function(response) {
                                if (response) {
                                    item.closest('tr').find('.stock').val(response.quantity || 0);
                                    item.closest('tr').find('.buying_price').val(response.buying_price || 0);
                                    item.closest('tr').find('.vat_percent').val(response.vat_percent || 0);
                                    item.closest('tr').find('.discount').val(response.discount_percent || 0);
                                    item.closest('tr').find('.maximum_discount').text((response.selling_price - response.buying_price) || 0);
                                    item.closest('tr').find('.unit_price').val(response.selling_price || 0);
                                    item.closest('tr').find('.total-selling-cost').text(response.selling_price || 0);

                                    calculate();
                                    calculateDiscountPercent();
                                } else {
                                    $('#add_stock').val(0);
                                }
                            }).always(function () {
                                isProcessing = false;
                            });
                        }
                        item.closest('tr').find('.product_name_text').text(response.inventory.product.name);
                        item.closest('tr').find('.product_name').val(response.inventory.product.name);
                        item.closest('tr').find('.product_code_text').text(response.inventory.serial_no);
                        item.closest('tr').find('.product_code').val(response.inventory.serial_no);
                        item.closest('tr').find('.batch').val(response.inventory.batch_no);
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
                    isProcessing = false;
                }).always(function () {
                    isProcessing = false;
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

            let totalQuantity = 0;
            let totalVatAmount = 0;
            let totalDisPrice = 0;
            let totalSellingPrice = 0;
            let discount = parseFloat($('.discount').val()) || 0;

            $("#product-container .product-item").each(function() {
                // $(this).find('.product_sl').text(productSl++);

                let quantity = parseFloat($(this).find('.quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                let buyingPrice = parseFloat($(this).find('.buying_price').val()) || 0;
                //let productDiscount = parseFloat($(this).find('.discount').val()) || 0;
                let vat_percent = parseFloat($(this).find('.vat_percent').val()) || 0;
                let discountAmount = parseFloat($(this).find('.discount_amt').val()) || 0;
                let discountPercent = parseFloat($(this).find('.discount').val()) || 0;
                //calculate vat
                let vat_amount = unitPrice * (vat_percent / 100);
                $(this).find('.vat_amount').text(vat_amount.toFixed(2));

                $(this).find('.maximum_discount').text((unitPrice - buyingPrice) * quantity);
                //alert(discountAmount);
                if(discountAmount > 0){
                    // if(discountAmount > quantity*(unitPrice - buyingPrice)){
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: 'Discount Exceeded',
                    //         text: `The entered Discount exceeds ! Please decrease discount.`
                    //     });
                    //     let notificationSound = document.getElementById('notification-error-audio');
                    //     notificationSound?.play();
                    //     $(this).find('.discount').val(0);
                    //     return false;
                    // }
                }
                // $(this).find('.total-selling-cost').text((quantity * unitPrice).toFixed(2));

                // Use the individual product discount if present
                let discountAmt = discountAmount;

                $(this).find('.discount_amt').val(discountAmt.toFixed(2));
                $(this).find('.total-selling-cost').text(((quantity * unitPrice)-discountAmt+vat_amount).toFixed(2));
                totalQuantity += quantity;
                totalSellingPrice += quantity * unitPrice;
                totalVatAmount += vat_amount;
                totalDisPrice += discountAmt;
            });

            totalDisPrice = Math.floor(totalDisPrice);
            totalVatAmount = Math.floor(totalVatAmount);

            $('#total_quantity').text(totalQuantity);
            $('#total_vat_amount').text((totalVatAmount).toFixed(2));
            $('#total_dis_price').text((totalDisPrice).toFixed(2));
            $('#total_selling_price').text((totalSellingPrice-totalDisPrice+totalVatAmount).toFixed(2));
            $('#sub_total').val((totalSellingPrice-totalDisPrice+totalVatAmount).toFixed(2));

            let total = totalSellingPrice - totalDisPrice + totalVatAmount;
            $('#total_discount').val((totalDisPrice).toFixed(2));
            $('#total_vat').val((totalVatAmount).toFixed(2));
            $('#total_amount').val(total.toFixed(2));
            $('#total').val(total.toFixed(2));

            $('#paid').val(total.toFixed(2));
            let paid = parseFloat($('#paid').val()) || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;

            let dueAmount = Math.ceil(total - paid - paid_two - paid_three);
            $('#due_amount').val(dueAmount.toFixed(2));
            $('#due_total').val(dueAmount.toFixed(2));

            $('#tr-next-payment').toggle(dueAmount > 0);
            $("#footer_area").toggle($("#product-container .product-item").length > 0);


            $('#total-quantity').text(totalQuantity);
            $('#sub_total').val(totalSellingPrice.toFixed(2));
            // $('#total-checkout-price').text(totalSellingPrice.toFixed(2));
            $('#total-checkout-price').text((totalSellingPrice-totalDisPrice+totalVatAmount).toFixed(2));
            // calculatePaid();
            if (totalSellingPrice > 0) {
                $('#btn-checkout').attr('disabled', false);
            } else {
                $('#btn-checkout').attr('disabled', true);
            }
        }
        function calculateDiscount() {
            $("#product-container .product-item").each(function() {
                let quantity = parseFloat($(this).find('.quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                let buyingPrice = parseFloat($(this).find('.buying_price').val()) || 0;
                let discountAmount = parseFloat($(this).find('.discount_amt').val()) || 0;

                let discountPercent = ((discountAmount/quantity)*100)/unitPrice;
                //alert(discountPercent);
                $(this).find('.discount').val(Math.floor(discountPercent * 100) / 100);
                calculate();
            });
        }
        function calculateDiscountPercent() {
            // alert('hhh');
            $("#product-container .product-item").each(function() {
                let quantity = parseFloat($(this).find('.quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                let buyingPrice = parseFloat($(this).find('.buying_price').val()) || 0;
                let discountPercent = parseFloat($(this).find('.discount').val()) || 0;

                let discountAmount = (unitPrice*quantity*discountPercent)/100;
                //alert(discountPercent);
                $(this).find('.discount_amt').val(discountAmount.toFixed(2));
                calculate();
            });
        }
        function calculateQuantity() {
            // alert('hhh');
            $("#product-container .product-item").each(function() {
                let quantity = parseFloat($(this).find('.quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                let buyingPrice = parseFloat($(this).find('.buying_price').val()) || 0;
                let discountPercent = parseFloat($(this).find('.discount').val()) || 0;

                let discountAmount = (unitPrice*quantity*discountPercent)/100;
                //alert(discountPercent);
                $(this).find('.discount_amt').val(discountAmount.toFixed(2));
                calculate();
            });
        }

        function calculatePaid(){
            var total = $('#total').val() || 0;
            let paid = $('#paid').val() || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;
            let dueAmount = total - paid - paid_two - paid_three;

            $('#due_total').val(dueAmount.toFixed(2));
            $('#due_amount').val(dueAmount.toFixed(2));
        }
        function calculatePaidTwo(){
            var total = $('#total').val() || 0;
            let paid = $('#paid').val() || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;
            let dueAmount = total - paid - paid_two - paid_three;

            $('#due_total').val(dueAmount.toFixed(2));
            $('#due_amount').val(dueAmount.toFixed(2));
        }
        function calculatePaidThree(){
            var total = $('#total').val() || 0;
            let paid = $('#paid').val() || 0;
            let paid_two = $('#paid_two').val() || 0;
            let paid_three = $('#paid_three').val() || 0;
            let dueAmount = total - paid - paid_two - paid_three;

            $('#due_total').val(dueAmount.toFixed(2));
            $('#due_amount').html(dueAmount.toFixed(2));
        }

    </script>
@endsection
