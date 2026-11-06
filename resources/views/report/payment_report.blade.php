@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Payment Receive Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.payment_report') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('start_date') }}" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('end_date') }}" id="end_date" name="end_date">
                                </div>
                            </div>

                             <div class="col-md-2"  id="sale_type">
                                <div class="form-group">
                                    <label>Sale Type </label>
                                    <select class="form-control select2" name="sale_type">
                                        <option value="">Select Option </option>
                                        <option value="1" {{ request('sale_type')==1?'selected':'' }}>Offline </option>
                                        <option value="2" {{ request('sale_type')==2?'selected':'' }}>Online </option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>Paid By </label>
                                    <select class="form-control select2" name="paid_by">
                                        <option value="">Select Option </option>
                                        <option value="1" {{ request('paid_by')==1?'selected':'' }}>Cash </option>
                                        <option value="2" {{ request('paid_by')==2?'selected':'' }}>Bank </option>
                                        <option value="3" {{ request('paid_by')==3?'selected':'' }}>Mobile Banking </option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label>Received By </label>
                                    <select class="form-control select2" name="user_by" id="user_by" data-placeholder="All User">
                                        <option value="">All User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('user_by', request('user_by')) == $user->id) ? 'selected' : '' }}> {{ $user->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if (count($payments) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Payment Receive Report</h3>
                            @if (request('start_date'))
                                <h6>Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h6>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped" style="width:100% !important;">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order No.</th>
                                    <th>Customer Name</th>
                                    <th>Address</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if (count($payments) > 0)
                                        @foreach($payments as $payment)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($payment->date)->format('d-M-Y') }}</td>
                                                <td>
                                                    <a target="_blank" href="{{ route('sale_receipt.details', ['order' => $payment->salesOrder->id]) }}">
                                                        {{ $payment->salesOrder->order_no??'' }}
                                                    </a>
                                                </td>
                                                <td>{{ $payment->salesOrder->customer->name??'' }}</td>
                                                <td>{{ $payment->salesOrder->customer->address??'' }}</td>
                                                <td>
                                                    @if ($payment->transaction_method == 1)
                                                        Cash
                                                    @elseif ($payment->transaction_method == 2)
                                                        Bank
                                                    @elseif ($payment->transaction_method == 3)
                                                        {{ $payment->mobileBanking->account_name??'' }}
                                                    @endif
                                                </td>
                                                <td>{{ number_format($payment->amount, 2) }}</td>
                                                <td><a target="_blank" href="{{ route('sale_receipt.payment_details', ['payment' => $payment->id]) }}">View</a></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                @php
                                    $grand_total = 0;
                                    if ($payments && count($payments) > 0){
                                        $grand_total += $payments->sum('amount');
                                    }
                                @endphp
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total</th>
                                        <td>{{ number_format($grand_total, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    @endif

@endsection

@section('script')
  <!-- date-range-picker -->
  <script src="{{ asset('themes/backend/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
  <script src="https://cdn.datatables.net/fixedheader/3.3.4/js/dataTables.fixedHeader.min.js"></script>
    <script>
        $(function () {
            $('#table').DataTable({
                fixedHeader: true,
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: true,
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
            });
            //Initialize Select2 Elements
            $('.select2').select2()
            $('#department').change(function(){
                let department = $(this).val();
                if(department == 1){
                    $('#sale_type').show();
                }else{
                    $('#sale_type').hide();
                }
            });
            $('#department').trigger('change');
            var dates = '{{ request()->get('date') }}';
            if (dates != '') {
                dateExplode = dates.split(' - ');

                //Date range picker
                $('#date').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    },
                    startDate: moment(dateExplode[0], "YYYY-MM-DD"),
                    endDate: moment(dateExplode[1], "YYYY-MM-DD"),
                });

                console.log('aise');
            } else {
                //Date range picker
                $('#date').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
            }

            $('#date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                table.ajax.reload();
            });

            $('#date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });

            $('#date, #type').change(function () {
                table.ajax.reload();
            });
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {
            $('#table').DataTable().destroy();
            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
