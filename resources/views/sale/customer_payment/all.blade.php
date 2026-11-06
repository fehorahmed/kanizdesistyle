@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Customer Payment
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Total</th>
                            <th>Opening Due</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pay">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <input type="hidden" name="customer_id" id="modal_cutomer_id">
                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>
                        <div class="form-group">
                            <label>Sale Type</label>
                            <select class="form-control" id="modal-sale-type" name="sale_type">
                                <option value="1">Offline</option>
                                <option value="2">Online</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Order</label>
                            <select class="form-control" id="modal-order" name="order">
                                <option value="">Select Order</option>
                            </select>
                        </div>
                        <input type="hidden" name="due" id="due">
                        <div id="modal-order-info" style="background-color: lightgrey; padding: 10px; border-radius: 3px;"></div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <select class="form-control" id="payment_type" name="payment_type">
                                        <option value="">Select Option</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank</option>
                                        <option value="3">Mobile Banking</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="mobile-bank-info">
                                <div class="form-group">
                                    <label>Mobile Banking Account</label>
                                    <select class="form-control" id="mobile_banking" name="mobile_banking">
                                        <option value="">Select Account</option>
                                        @foreach ($mobileBankings as $mobileBanking)
                                            <option value="{{ $mobileBanking->id }}">
                                                {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="bank-info">
                                <div class="form-group">
                                    <label>Account</label>
                                    <select class="form-control account" name="account">
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
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control" name="amount" placeholder="Enter Amount" id="amount">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Type (Additional)</label>
                                    <select class="form-control" id="payment_type_two" name="payment_type_two">
                                        <option value="">Select Option</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank</option>
                                        <option value="3">Mobile Banking</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="mobile-bank-info-two">
                                <div class="form-group">
                                    <label>Mobile Banking Account</label>
                                    <select class="form-control" id="mobile_banking_two" name="mobile_banking_two">
                                        <option value="">Select Account</option>
                                        @foreach ($mobileBankings as $mobileBanking)
                                            <option value="{{ $mobileBanking->id }}">
                                                {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="bank-info-two">
                                <div class="form-group">
                                    <label>Account</label>
                                    <select class="form-control account_two" name="account_two">
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
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control" value="0" name="amount_two" placeholder="Enter Amount" id="amount_two">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Type (Additional)</label>
                                    <select class="form-control" id="payment_type_three" name="payment_type_three">
                                        <option value="">Select Option</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank</option>
                                        <option value="3">Mobile Banking</option>
                                    </select> 
                                </div>
                            </div>
                            <div class="col-md-4" id="mobile-bank-info-three">
                                <div class="form-group">
                                    <label>Mobile Banking Account</label>
                                    <select class="form-control" id="mobile_banking_three" name="mobile_banking_three">
                                        <option value="">Select Account</option>
                                        @foreach ($mobileBankings as $mobileBanking)
                                            <option value="{{ $mobileBanking->id }}">
                                                {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="bank-info-three">
                                <div class="form-group">
                                    <label>Account</label>
                                    <select class="form-control account_three" name="account_three">
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
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control" value="0" name="amount_three" placeholder="Enter Amount" id="amount_three">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control date-picker" name="date" value="{{ date('d-m-Y') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-pay">Pay</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
                    <form id="modal-form-refund" enctype="multipart/form-data" name="modal-form">
                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name-refund" disabled>
                        </div>
                        <div class="form-group">
                            <label>Order</label>
                            <select class="form-control" id="modal-order-refund" name="order">
                                <option value="">Select Order</option>
                            </select>
                        </div>

                        <div id="modal-order-info-refund" style="background-color: lightgrey; padding: 10px; border-radius: 3px;"></div>

                        <div class="form-group">
                            <label>Payment Type</label>
                            <select class="form-control" id="modal-pay-type-refund" name="payment_type">
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Mobile Banking</option>
                            </select>
                        </div>

                        <div id="modal-bank-info-refund">
                            <div class="form-group">
                                <label>Bank</label>
                                <select class="form-control modal-bank" name="bank">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Branch</label>
                                <select class="form-control modal-branch" name="branch">
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Account</label>
                                <select class="form-control modal-account" name="account">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Cheque No.</label>
                                <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No.">
                            </div>

                            <div class="form-group">
                                <label>Cheque Image</label>
                                <input class="form-control" name="cheque_image" type="file">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" name="amount" placeholder="Enter Amount">
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="date-refund" name="date" value="{{ date('Y-m-d') }}" autocomplete="off">
                            </div>
                            <!-- /.input group -->
                        </div>

                        <div class="form-group">
                            <label>Note</label>
                            <input class="form-control" name="note" placeholder="Enter Note">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-refund">Pay</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        var due;

        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customer_payment.datatable') }}"
                },
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'address', name: 'address'},
                    {data: 'paid', name: 'paid', orderable: false},
                    {data: 'due', name: 'due', orderable: false},
                    {data: 'total', name: 'total', orderable: false},
                    {data: 'current_opening_due', name: 'current_opening_due', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": true, "autoWidth": false,"colReorder": true,
            });

            //Date picker
            $('#date, #next-payment-date, #date-refund').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                // moment.tz.setDefault("Europe/Berlin");
            });

            $('body').on('click', '.btn-pay', function () {
                var customerId = $(this).data('id');
                var saleType = $('#modal-sale-type').val();
                var customerName = $(this).data('name');
                var department_id = 1;
                $('#modal-order').html('<option value="">Select Order</option>');
                $('#modal-order-info').hide();
                $('#modal-name').val(customerName);
                $('#modal_cutomer_id').val(customerId);

                $.ajax({
                    method: "GET",
                    url: "{{ route('customer_payment.get_orders') }}",
                    data: { customerId: customerId, saleType: saleType, department_id: department_id }
                }).done(function( response ) {
                    // console.log(response);
                    $.each(response, function( index, item ) {
                        $('#modal-order').append('<option value="'+item.id+'">'+item.order_no+'</option>');
                    });

                    checkNextPayment();
                    $('#modal-pay').modal('show');
                });
            });

            $('#modal-sale-type').change(function(){
                var customerId = $('#modal_cutomer_id').val();
                var saleType = $(this).val();
                var department_id = 1;
                $('#modal-order').html('<option value="">Select Order</option>');
                $('#modal-order-info').hide();
                $.ajax({
                    method: "GET",
                    url: "{{ route('customer_payment.get_orders') }}",
                    data: { customerId: customerId, saleType: saleType, department_id: department_id }
                }).done(function( response ) {
                    // console.log(response);
                    $.each(response, function( index, item ) {
                        $('#modal-order').append('<option value="'+item.id+'">'+item.order_no+'</option>');
                    });

                    checkNextPayment();
                    $('#modal-pay').modal('show');
                });
            });

            $('#modal-btn-pay').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('customer_payment.make_payment') }}",
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
                                location.reload();
                                // window.location.href = response.redirect_url;
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

            $('#modal-order').change(function () {
                var orderId = $(this).val();
                $('#modal-order-info').hide();

                if (orderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_order_details') }}",
                        data: { orderId: orderId }
                    }).done(function( response ) {
                        due = parseFloat(response.due).toFixed(2);
                        $('#modal-order-info').html('<strong>Total: </strong>৳'+parseFloat(response.total).toFixed(2)+' <strong>Paid: </strong>৳'+parseFloat(response.paid).toFixed(2)+' <strong>Due: </strong>৳'+parseFloat(response.due).toFixed(2));
                        $('#modal-order-info').show();
                        $('#due').val(due);
                        $('#amount').val(due);
                    });
                }
            });

            $('#payment_type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info').hide();
                    $('#mobile-bank-info').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info').show();
                    }
                } else if($(this).val() == '2') {
                    $('#bank-info').show();
                    $('#mobile-bank-info').hide();
                }else {
                    $('#bank-info').hide();
                    $('#mobile-bank-info').hide();
                }
            });
            $('#payment_type').trigger('change');

            $('#payment_type_two').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info-two').hide();
                    $('#mobile-bank-info-two').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info-two').show();
                    }
                } else if($(this).val() == '2') {
                    $('#bank-info-two').show();
                    $('#mobile-bank-info-two').hide();
                }else {
                    $('#bank-info-two').hide();
                    $('#mobile-bank-info-two').hide();
                }
            });
            $('#payment_type_two').trigger('change');

            $('#payment_type_three').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info-three').hide();
                    $('#mobile-bank-info-three').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info-three').show();
                    }
                } else if($(this).val() == '2') {
                    $('#bank-info-three').show();
                    $('#mobile-bank-info-three').hide();
                }else {
                    $('#bank-info-three').hide();
                    $('#mobile-bank-info-three').hide();
                }
            });
            $('#payment_type_three').trigger('change');

            $('body').on('keyup','#amount',function(){
                let amount = $(this).val();
                let due = $('#due').val();
                let dueAmount = due - amount;
                if(dueAmount > 0){
                    $('#amount_two').val(dueAmount.toFixed(2));
                }
            });    
      
            $('body').on('keyup','#amount_two',function(){
                let amount = $('#amount').val();
                let amountTwo = $(this).val();
                let due = $('#due').val();
                let dueAmount = due - amount - amountTwo;
                if(dueAmount > 0){
                    $('#amount_three').val(dueAmount.toFixed(2));
                }
            });

            checkNextPayment();
            $('#amount').keyup(function () {
                checkNextPayment();
            });

            // Refund
            $('body').on('click', '.btn-refund', function () {
                var customerId = $(this).data('id');
                var customerName = $(this).data('name');
                $('#modal-order-refund').html('<option value="">Select Order</option>');
                $('#modal-order-info-refund').hide();
                $('#modal-name-refund').val(customerName);

                $.ajax({
                    method: "GET",
                    url: "{{ route('customer_payment.get_refund_orders') }}",
                    data: { customerId: customerId }
                }).done(function( response ) {
                    $.each(response, function( index, item ) {
                        $('#modal-order-refund').append('<option value="'+item.id+'">'+item.order_no+'</option>');
                    });

                    $('#modal-refund').modal('show');
                });
            });

            $('#modal-pay-type-refund').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info-refund').hide();
                } else {
                    $('#modal-bank-info-refund').show();
                }
            });

            $('#modal-pay-type-refund').trigger('change');

            $('#modal-order-refund').change(function () {
                var orderId = $(this).val();
                $('#modal-order-info-refund').hide();

                if (orderId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_order_details') }}",
                        data: { orderId: orderId }
                    }).done(function( response ) {
                        due = response.due.toFixed(2);
                        $('#modal-order-info-refund').html('<strong>Total: </strong>৳'+response.total.toFixed(2)+' <strong>Paid: </strong>৳'+response.paid.toFixed(2)+' <strong>Due: </strong>৳'+response.due.toFixed(2)+' <strong>Refund: </strong>৳'+response.refund.toFixed(2));
                        $('#modal-order-info-refund').show();
                    });
                }
            });

            $('#modal-btn-refund').click(function () {
                var formData = new FormData($('#modal-form-refund')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('customer_payment.make_refund') }}",
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
        });

        function checkNextPayment() {
            var paid = $('#amount').val();

            if (paid == '' || paid < 0 || !$.isNumeric(paid))
                paid = 0;

            if (parseFloat(paid) >= due)
                $('#fg-next-payment-date').hide();
            else
                $('#fg-next-payment-date').show();
        }
    </script>
@endsection
