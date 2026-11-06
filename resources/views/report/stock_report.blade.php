@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Stock Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.stock_report') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-6"  id="product">
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
    @if (count($inventories) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Stock Report</h3>
                            @if(request('start_date') && request('end_date'))
                                <span><b> Date : </b>{{ date('d-m-Y',strtotime(request('start_date'))) }} <b>to</b> {{ date('d-m-Y',strtotime(request('end_date'))) }}</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped" style="width: 100% !important;">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Product Name</th>
                                    <th>Batch No</th>
                                    <th>Costing Value</th>
                                    <th>Selling Value </th>
                                    <th>In Qty</th>
                                    <th>Out Qty</th>
                                    <th>Remaining Qty</th>
                                </tr>
                                </thead>
                                @php
                                    $totalPurchaseQty = 0;            
                                    $totalSaleQty = 0;            
                                    $totalClosingCostingPrice = 0;            
                                    $totalClosingMrpPrice = 0;            
                                @endphp
                                <tbody>
                                    @foreach ($inventories as $index => $inventory)
                                        @php
                                           $totalPurchaseQty += $inventory->purchaseQty();            
                                           $totalSaleQty += $inventory->saleQty();
                                           $totalClosingCostingPrice += $inventory->unit_price*$inventory->pack_quantity;            
                                           $totalClosingMrpPrice += $inventory->selling_price*$inventory->pack_quantity;        
                                        @endphp
                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($inventory->created_at)->format('d-M-Y') }}</td>
                                            <td>{{ $inventory->product->name ?? '' }}</td>
                                            <td>{{ $inventory->batch_no }}</td>
                                            <td>{{ number_format($inventory->unit_price,2) }}</td>
                                            <td>{{ number_format($inventory->selling_price,2) }}</td>
                                            <td>{{ $inventory->purchaseQty() }}</td>
                                            <td>{{ $inventory->saleQty() }}</td>
                                            <td>{{ $inventory->quantity }}</td>
                                        </tr>
                                    @endforeach
                                    
                                </tbody>
                                <tr>
                                    <th class="text-center" colspan="6">Total</th>
                                    <th>{{ $totalPurchaseQty }}</th>
                                    <th>{{ $totalSaleQty }}</th>
                                    <th>{{ $inventories->sum('quantity') }}</th>
                                </tr>
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
                // "responsive": true, "autoWidth": false,"colReorder": true,
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
