@extends('layouts.app')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection
@section('title', 'Sale Receipt')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" autocomplete="off" name="start_date" id="start_date"
                                    class="form-control date-picker">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" autocomplete="off" name="end_date" id="end_date"
                                    class="form-control date-picker">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 30px;">
                                <button class="btn btn-info">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Order No</th>
                                <th>Customer Name</th>
                                <th>Mobile No</th>
                                <th>Total</th>
                                <th>Return</th>
                                <th>Deduction</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="5">Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
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
                        <input type="hidden" name="order" id="order-id">

                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" id="modal-name" disabled>
                        </div>

                        <div class="form-group">
                            <label>Order No.</label>
                            <input class="form-control" id="modal-order-id" disabled>
                        </div>
                        <input type="hidden" name="due" id="due">
                        <div id="modal-order-info" style="background-color: lightgrey; padding: 10px; border-radius: 3px;">
                        </div>
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
                                                {{ $account->branch->name ?? '' }} -
                                                {{ $account->bank->name ?? '' }}
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
                                                {{ $account->branch->name ?? '' }} -
                                                {{ $account->bank->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control" value="0" name="amount_two"
                                        placeholder="Enter Amount" id="amount_two">
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
                                                {{ $account->branch->name ?? '' }} -
                                                {{ $account->bank->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input class="form-control" value="0" name="amount_three"
                                        placeholder="Enter Amount" id="amount_three">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control date-picker" id="date" name="date"
                                    value="{{ date('Y-m-d') }}" autocomplete="off">
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

    <div class="modal fade" id="modal-status">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Status Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-status-form" enctype="multipart/form-data" name="modal-form">
                        <input type="hidden" name="status_order" id="status-order-id">
                        <div class="form-group">
                            <label>Order Status</label>
                            <select class="form-control" id="modal-status-type" name="status">
                                <option value="0">Pending</option>
                                <option value="1">Processing</option>
                                <option value="2">In-transit</option>
                                <option value="3">Completed</option>
                            </select>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-btn-status">Submit</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var due;
        $(function() {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('sales_return_fm_receipt_online.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        const urlParams = new URLSearchParams(window.location.search);
                        d.type = urlParams.get('type'); // Dynamically pass value from url
                        d.paid_by = $('#paid_by').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer.name'
                    },
                    {
                        data: 'customer_mobile_no',
                        name: 'customer.mobile_no'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'return',
                        name: 'return'
                    },
                    {
                        data: 'deduction',
                        name: 'deduction'
                    },
                    {
                        data: 'paid',
                        name: 'paid'
                    },
                    {
                        data: 'due',
                        name: 'due'
                    },
                    // {data: 'next_payment', name: 'next_payment'},
                    {
                        data: 'user',
                        name: 'user.name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": true,
                "autoWidth": false,
                "colReorder": true,
                order: [
                    [0, "desc"]
                ],
                "footerCallback": function (row, data, start, end, display) {
                    var api = this.api();

                    var intVal = function (i) {
                        if (typeof i === 'string') {
                            return parseFloat(i.replace(/[^\d.-]/g, '')) || 0;
                        } else if (typeof i === 'number') {
                            return i;
                        }
                        return 0;
                    };

                    // Calculate total sum
                    var total = api
                        .column(5, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var returnedAmount = api
                        .column(6, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var deduction = api
                        .column(7, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var paid = api
                        .column(8, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var due = api
                        .column(9, { search: 'applied' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(5).footer()).html('৳' + total.toLocaleString());
                    $(api.column(6).footer()).html('৳' + returnedAmount.toLocaleString());
                    $(api.column(7).footer()).html('৳' + deduction.toLocaleString());
                    $(api.column(8).footer()).html('৳' + paid.toLocaleString());
                    $(api.column(9).footer()).html('৳' + due.toLocaleString());
                }
            });

            //Date picker
            $('#date, #next-payment-date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            //
            $('#start_date, #end_date').change(function() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                if (start_date && end_date) {
                    table.ajax.reload();
                }
            });
            $('#paid_by').change(function() {
                var paid_by = $('#paid_by').val();
                if (paid_by) {
                    table.ajax.reload();
                }
            });

            $('body').on('click', '.btn-payment', function() {
                var orderId = $(this).data('id');

                $.ajax({
                    method: "GET",
                    url: "{{ route('get_order_details') }}",
                    data: {
                        orderId: orderId
                    }
                }).done(function(response) {
                    $('#modal-name').val(response.customer.name);
                    $('#modal-order-id').val(response.order_no);
                    $('#order-id').val(response.id);
                    due = parseFloat(response.due).toFixed(2);
                    $('#modal-order-info').html('<strong>Total: </strong>৳' + parseFloat(response
                        .total-response.return_amount+response.deduction).toFixed(2) + ' <strong>Paid: </strong>৳' + parseFloat(
                        response.paid).toFixed(2) + ' <strong>Due: </strong>৳' + parseFloat(
                        response.due).toFixed(2));
                    $('#due').val(due);
                    $('#amount').val(due);
                    checkNextPayment();
                    $('#modal-pay').modal('show');
                });
            });

            $('body').on('click', '.btn-status', function() {
                var orderId = $(this).data('id');
                var orderStatus = $(this).data('status');
                if (orderId) {
                    $('#status-order-id').val(orderId);
                    $('#modal-status-type').val(orderStatus);

                    $('#modal-status').modal('show');
                }
            });

            $('#payment_type').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info').hide();
                    $('#mobile-bank-info').hide();
                    if ($(this).val() == '3') {
                        $('#mobile-bank-info').show();
                    }
                } else if ($(this).val() == '2') {
                    $('#bank-info').show();
                    $('#mobile-bank-info').hide();
                } else {
                    $('#bank-info').hide();
                    $('#mobile-bank-info').hide();
                }
            });
            $('#payment_type').trigger('change');

            $('#payment_type_two').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info-two').hide();
                    $('#mobile-bank-info-two').hide();
                    if ($(this).val() == '3') {
                        $('#mobile-bank-info-two').show();
                    }
                } else if ($(this).val() == '2') {
                    $('#bank-info-two').show();
                    $('#mobile-bank-info-two').hide();
                } else {
                    $('#bank-info-two').hide();
                    $('#mobile-bank-info-two').hide();
                }
            });
            $('#payment_type_two').trigger('change');

            $('#payment_type_three').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info-three').hide();
                    $('#mobile-bank-info-three').hide();
                    if ($(this).val() == '3') {
                        $('#mobile-bank-info-three').show();
                    }
                } else if ($(this).val() == '2') {
                    $('#bank-info-three').show();
                    $('#mobile-bank-info-three').hide();
                } else {
                    $('#bank-info-three').hide();
                    $('#mobile-bank-info-three').hide();
                }
            });
            $('#payment_type_three').trigger('change');

            $('body').on('keyup', '#amount', function() {
                let amount = $(this).val();
                let due = $('#due').val();
                let dueAmount = due - amount;
                if (dueAmount > 0) {
                    $('#amount_two').val(dueAmount.toFixed(2));
                }
            });

            $('body').on('keyup', '#amount_two', function() {
                let amount = $('#amount').val();
                let amountTwo = $(this).val();
                let due = $('#due').val();
                let dueAmount = due - amount - amountTwo;
                if (dueAmount > 0) {
                    $('#amount_three').val(dueAmount.toFixed(2));
                }
            });

            $('#modal-btn-status').click(function() {
                var formData = new FormData($('#modal-status-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('sale_receipt.status_change') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-status').modal('hide');
                            Swal.fire(
                                'Changed!',
                                response.message,
                                'success'
                            ).then((result) => {
                                //location.reload();
                                // window.location.href = response.redirect_url;
                                table.ajax.reload();
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

            $('#modal-btn-pay').click(function() {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('sale_receipt.make_payment') }}",
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

            checkNextPayment();
            $('#amount').keyup(function() {
                checkNextPayment();
            });

            $('body').on('click', '.btn-delete', function() {
                var orderId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the order This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('order_receipt_delete') }}",
                            type: "POST",
                            data: {
                                orderId: orderId
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while deleting the order.',
                                    'error'
                                );
                            }
                        });
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
