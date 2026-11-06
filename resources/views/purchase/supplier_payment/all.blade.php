@extends('layouts.app')
@section('title','Supplier Payment')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Opening Due</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->mobile }}</td>
                                <td>৳{{ number_format($supplier->paid, 2) }}</td>
                                <td>৳{{ number_format($supplier->due, 2) }}</td>
                                <td>৳{{ number_format($supplier->opening_due-$supplier->opening_paid, 2) }}</td>
                                <td>
                                    <a class="btn btn-info btn-sm btn-pay" role="button" data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}">Pay</a>
                                    <a class="btn btn-primary btn-sm" href="{{ route('supplier_payments', ['supplier'=>$supplier->id]) }}" target="_blank"> Details </a>

                                    {{-- @if($supplier->refund > 0)
                                        <a class="btn btn-success btn-sm btn-refund" role="button" data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}">Refund</a>
                                    @endif --}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pay" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-has-error">
                                    <label>Name</label>
                                    <input class="form-control" id="modal-name" disabled>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-group-has-error">
                                    <label>Order</label>
                                    <select class="form-control" id="modal-order" name="order">
                                        <option value="">Select Order</option>
                                    </select>
                                </div>
                                <div id="modal-order-info" style="background-color: lightgrey; padding: 10px; border-radius: 3px;"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-group-has-error">
                                    <label>Payment Type</label>
                                    <select class="form-control select2" id="modal-pay-type" name="payment_type">
                                        <option value="1">Cash</option>
                                        <option value="2">Bank</option>
                                        <option value="3">Mobile Banking</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="mobile-bank-info">
                            <div class="row">
                                <div class="col-md-12">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="modal-bank-info">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-has-error">
                                        <label> Bank Account</label>
                                        <select class="form-control select2 modal-account" name="account">
                                            <option value="">Select Bank Account</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->account_no }} -
                                                    {{ $account->branch->name??'' }} -
                                                    {{ $account->bank->name??'' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group form-group-has-error">
                                        <label>Cheque No.</label>
                                        <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No.">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group form-group-has-error">
                                        <label>Cheque Image</label>
                                        <input class="form-control" name="cheque_image" type="file">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-has-error">
                                    <label>Amount</label>
                                    <input class="form-control" name="amount" placeholder="Enter Amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-has-error">
                                    <label>Date</label>
                                    <input type="text" class="form-control date-picker" id="date" name="date" value="{{ date('d-m-Y') }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-has-error">
                                    <label>Note</label>
                                    <input class="form-control" name="note" placeholder="Enter Note">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal-btn-pay" class="btn btn-primary">Save</button>
                </div>
            </div>

        </div>

    </div>
    <!-- /.modal -->

    <div class="modal fade" id="modal-refund">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Refund Information</h4>
                </div>
                <div class="modal-body">
                    <form id="refund-form" enctype="multipart/form-data" name="refund-form">
                        <div class="form-group row">
                            <label>Name</label>
                            <input class="form-control" id="modal-name-refund" disabled>
                        </div>

                        <div class="form-group row">
                            <label>Order</label>
                            <select class="form-control" id="modal-order-refund" name="order">
                                <option value="">Select Order</option>
                            </select>
                        </div>

                        <div id="modal-order-info-refund" style="background-color: lightgrey; padding: 10px; border-radius: 3px;"></div>

                        <div class="form-group row">
                            <label>Payment Type</label>
                            <select class="form-control" id="modal-pay-type-refund" name="payment_type">
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Mobile Banking</option>
                            </select>
                        </div>

                        <div id="modal-bank-info-refund">
                            <div class="form-group row">
                                <label>Bank</label>
                                <select class="form-control modal-bank" name="bank">
                                    <option value="">Select Bank</option>

                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group row">
                                <label>Branch</label>
                                <select class="form-control modal-branch" name="branch">
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <div class="form-group row">
                                <label>Account</label>
                                <select class="form-control modal-account" name="account">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="form-group row">
                                <label>Cheque No.</label>
                                <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No.">
                            </div>

                            <div class="form-group row">
                                <label>Cheque Image</label>
                                <input class="form-control" name="cheque_image" type="file">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label>Amount</label>
                            <input class="form-control" name="amount" placeholder="Enter Amount">
                        </div>

                        <div class="form-group row">
                            <label>Date</label>
                            <input type="text" class="form-control date-picker" id="date-refund" name="date" value="{{ date('Y-m-d') }}" autocomplete="off">
                        </div>

                        <div class="form-group row">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-refund">Refund</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('script')
    <script>
        $(function () {
            var table = $('#table').DataTable();

            //Date picker
            $('#date, #date-refund').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('body').on('click', '.btn-pay', function () {
                var supplierId = $(this).data('id');
                var supplierName = $(this).data('name');
                $('#modal-order').html('<option value="">Select Order</option>');
                $('#modal-order-info').hide();
                $('#modal-name').val(supplierName);

                $.ajax({
                    method: "GET",
                    url: "{{ route('supplier_payment.get_orders') }}",
                    data: { supplierId: supplierId }
                }).done(function( response ) {
                    $.each(response, function( index, item ) {
                        $('#modal-order').append('<option value="'+item.id+'">'+item.order_no+'</option>');
                    });

                    $('#modal-pay').modal('show');
                });
            });

            $('#modal-btn-pay').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('supplier_payment.make_payment') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-pay').modal('hide');
                            Swal.fire(
                                'Paid!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });

            $('#modal-pay-type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info').hide();
                    $('#mobile-bank-info').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info').show();
                    }
                } else if($(this).val() == '2') {
                    $('#modal-bank-info').show();
                    $('#mobile-bank-info').hide();
                }else {
                    $('#modal-bank-info').hide();
                    $('#mobile-bank-info').hide();
                }
            });
            $('#modal-pay-type').trigger('change');

            $('#modal-order').change(function () {
                var orderId = $(this).val();
                $('#modal-order-info').hide();

                if (orderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('supplier_payment.order_details') }}",
                        data: { orderId: orderId }
                    }).done(function( response ) {
                        $('#modal-order-info').html('<strong>Total: </strong>৳'+parseFloat(response.total).toFixed(2)+' <strong>Paid: </strong>৳'+parseFloat(response.paid).toFixed(2)+' <strong>Due: </strong>৳'+parseFloat(response.due).toFixed(2));
                        // $('#modal-order-info').html('<strong>Total: </strong>৳'+parseFloat(response.total).toFixed(2)+' <strong>Paid: </strong>৳'+parseFloat(response.paid).toFixed(2)+' <strong>Due: </strong>৳'+parseFloat(response.due).toFixed(2)+' <strong>Refund: </strong>৳'+parseFloat(response.refund).toFixed(2));
                        $('#modal-order-info').show();
                    });
                }
            });

            $('.modal-bank').change(function () {
                var bankId = $(this).val();
                $('.modal-branch').html('<option value="">Select Branch</option>');
                $('.modal-account').html('<option value="">Select Account</option>');

                if (bankId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_branch') }}",
                        data: { bankId: bankId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            $('.modal-branch').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('.modal-branch').trigger('change');
                    });
                }

                $('.modal-branch').trigger('change');
            });

            $('.modal-branch').change(function () {
                var branchId = $(this).val();
                $('.modal-account').html('<option value="">Select Account</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_bank_account') }}",
                        data: { branchId: branchId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            $('.modal-account').append('<option value="'+item.id+'">'+item.account_no+'</option>');
                        });
                    });
                }
            });

            // Refund
            $('body').on('click', '.btn-refund', function () {
                var supplierId = $(this).data('id');
                var supplierName = $(this).data('name');
                $('#modal-order-refund').html('<option value="">Select Order</option>');
                $('#modal-order-info-refund').hide();
                $('#modal-name-refund').val(supplierName);

                $.ajax({
                    method: "GET",
                    url: "{{ route('supplier_payment.get_refund_orders') }}",
                    data: { supplierId: supplierId }
                }).done(function( response ) {
                    $.each(response, function( index, item ) {
                        $('#modal-order-refund').append('<option value="'+item.id+'">'+item.order_no+'</option>');
                    });

                    $('#modal-refund').modal('show');
                });
            });

            $('#modal-pay-type-refund').change(function () {
                if ($(this).val() == '2') {
                    $('#modal-bank-info-refund').show();
                } else {
                    $('#modal-bank-info-refund').hide();
                }
            });

            $('#modal-pay-type-refund').trigger('change');

            $('#modal-order-refund').change(function () {
                var orderId = $(this).val();
                $('#modal-order-info-refund').hide();

                if (orderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('supplier_payment.order_details') }}",
                        data: { orderId: orderId }
                    }).done(function( response ) {
                        $('#modal-order-info-refund').html('<strong>Total: </strong>৳'+response.total.toFixed(2)+' <strong>Paid: </strong>৳'+response.paid.toFixed(2)+' <strong>Due: </strong>৳'+response.due.toFixed(2)+' <strong>Refund: </strong>৳'+response.refund.toFixed(2));
                        $('#modal-order-info-refund').show();
                    });
                }
            });

            $('#modal-btn-refund').click(function () {
                var formData = new FormData($('#refund-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('supplier_payment.make_refund') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-refund').modal('hide');
                            Swal.fire(
                                'Paid!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        })
    </script>
@endsection
