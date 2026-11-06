@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Sale Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.sale_report') }}">
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
                                    <label>Paid Type</label>
                                    <select class="form-control select2" name="type">
                                        <option value="">All </option>
                                        <option value="1" {{ old('type',request('type'))==1?'selected':'' }}>Paid</option>
                                        <option value="2" {{ old('type',request('type'))==2?'selected':'' }}>Due</option>
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
    @if (count($orders) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Sale Report</h3>
                            @if (request('start_date'))
                                <h6>Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h6>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="display table table-bordered table-striped table-striped-custom" style="width:100% !important;">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order No.</th>
                                    <th>Customer Name</th>
                                    <th>Address</th>
                                    <th>SubTotal</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Cash Paid</th>
                                    <th>Bank Paid</th>
                                    <th>MobileBanking Paid</th>
                                    <th>Due</th>
                                </tr>
                                </thead>
                                @php
                                    $grand_cash_paid = 0;
                                    $grand_bank_paid = 0;
                                    $grand_mobilebanking_paid = 0;
                                @endphp
                                
                                <tbody>
                                    @foreach($orders as $order)
                                        @php
                                            $grand_cash_paid += $order->payments->where('transaction_method',1)->sum('amount');
                                            $grand_bank_paid += $order->payments->where('transaction_method',2)->sum('amount');
                                            $grand_mobilebanking_paid += $order->payments->where('transaction_method',3)->sum('amount');
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                            <td>
                                                <a target="_blank" href="{{ route('sale_receipt.details', ['order' => $order->id]) }}">
                                                    {{ $order->order_no }}
                                                </a>
                                            </td>
                                            <td>{{ $order->customer->name??'' }}</td>
                                            <td>{{ $order->customer->address??'' }}</td>
                                            <td>{{ number_format($order->sub_total,2) }}</td>
                                            <td>{{ number_format($order->discount,2) }}</td>
                                            <td>{{ number_format($order->total, 2) }}</td>
                                            <td>{{ number_format($order->paid, 2) }}</td>
                                            <td>{{ number_format($order->payments->where('transaction_method',1)->sum('amount'), 2) }}</td>
                                            <td>{{ number_format($order->payments->where('transaction_method',2)->sum('amount'), 2) }}</td>
                                            <td>{{ number_format($order->payments->where('transaction_method',3)->sum('amount'), 2) }}</td>
                                            <td>{{ number_format($order->due, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    
                                </tbody>
                                    @php
                                        $grand_sub_total = $orders->sum('sub_total');
                                        $grand_discount = $orders->sum('discount');
                                        $grand_total = $orders->sum('total');
                                        $grand_paid = $orders->sum('paid');
                                        $grand_due = $orders->sum('due');
                                    @endphp
                                    {{-- <tfoot> --}}
                                        <tr>
                                            <th colspan="4" class="text-right">Total</th>
                                            <td>{{ number_format($grand_sub_total, 2) }}</td>
                                            <td>{{ number_format($grand_discount, 2) }}</td>
                                            <td>{{ number_format($grand_total, 2) }}</td>
                                            <td>{{ number_format($grand_paid, 2) }}</td>
                                            <td>{{ number_format($grand_cash_paid, 2) }}</td>
                                            <td>{{ number_format($grand_bank_paid, 2) }}</td>
                                            <td>{{ number_format($grand_mobilebanking_paid, 2) }}</td>
                                            <td>{{ number_format($grand_due, 2) }}</td>
                                        </tr>
                                    {{-- </tfoot> --}}

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
