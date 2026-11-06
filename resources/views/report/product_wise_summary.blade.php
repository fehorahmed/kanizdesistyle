@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Product wise Summary Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.product_wise_summary') }}">
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
                            <div class="col-md-2"  id="product">
                                <div class="form-group">
                                    <label>Product </label>
                                    <select class="form-control select2" name="product">
                                        <option value="">All Product </option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ request('product')==$product->id?'selected':'' }}>{{ $product->name }}</option>
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
    @if (count($selected_products) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Product wise Summary Report</h3>
                            @if (request('start_date'))
                                <h6>Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h6>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped" style="width: 100% !important;">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Product Name</th>
                                    <th>Purchase Qty</th>
                                    <th>Sale Qty</th>
                                    <th>Stock Qty</th>
                                    {{-- <th>Stock Value</th> --}}
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selected_products as $index => $product)
                                    @php

                                        $start_date = \Carbon\Carbon::parse(request('start_date'))->format('Y-m-d');
                                        $end_date = \Carbon\Carbon::parse(request('end_date'))->format('Y-m-d');
                                        // dd($start_date);
                        
                                        $purchase_qty = \Illuminate\Support\Facades\DB::table('purchase_inventory_logs')
                                            ->where('product_id', $product->id)
                                            ->where('type', 1)
                                            ->whereBetween('date', [$start_date, $end_date])
                                            ->sum('quantity');
                                        
                                        $sale_qty = \Illuminate\Support\Facades\DB::table('purchase_inventory_logs')
                                            ->where('product_id', $product->id)
                                            ->where('type', 2)
                                            ->whereBetween('date', [$start_date, $end_date])
                                            ->sum('quantity');
                                            
                                    @endphp
                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $purchase_qty }}</td>
                                            <td>{{ $sale_qty }}</td>
                                            <td>{{ $purchase_qty - $sale_qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
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
