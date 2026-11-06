@extends('layouts.app')
@section('style')
    {{-- <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}"> --}}
@endsection
@section('title','Stock Statement Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.stock_statement') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('start_date') ?? date('d-m-Y') }}" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('end_date') ?? date('d-m-Y') }}" id="end_date" name="end_date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Product </label>
                                    <select class="form-control select2" name="product" id="product">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ request('product')==$product->id?'selected':'' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Batch </label>
                                    <select class="form-control select2" name="batch" id="batch">
                                        <option value="">Select Option</option>
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
    @if (count($logs) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Stock Statement Report</h3>
                            @if(request('start_date'))
                                <span><b> Date : </b>{{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Product Name</th>
                                        <th>Date</th>
                                        <th>Quantity(In)</th>
                                        <th>Quantity(Out)</th>
                                        <th>Balance Qty</th>
                                    </tr>
                                </thead>
                                @php
                                    $balance = 0;
                                @endphp
                                <tbody>
                                    @foreach ($logs as $index => $log)
                                        @php
                                            $inQty = 0;
                                            $outQty = 0;
                                            if($log->type == 1){
                                                $inQty = $log->quantity;
                                            }elseif($log->type == 2){
                                                $outQty = $log->quantity;
                                            }
                                            $balance += ($inQty - $outQty);
                                        @endphp
                                        <tr>
                                            <td>{{ $index++ }}</td>
                                            <td>{{ $log->product->name ?? '' }}</td>
                                            <td>{{ $log->date }}</td>
                                            <td>{{ $inQty }}</td>
                                            <td>{{ $outQty }}</td>
                                            <td>{{ $balance }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-center">Total</th>
                                        <th>{{ $totalPurchaseQty }}</th>
                                        <th></th>
                                        <th>{{ number_format($totalInCostingValue, 2) }}</th>
                                        <th>{{ $totalSaleQty }}</th>
                                        <th></th>
                                        <th>{{ number_format($totalOutCostingValue, 2) }}</th>
                                        <th>{{ $totalClosingQty }}</th>
                                        <th>{{ number_format($totalClosingCostingPrice, 2) }}</th>
                                        <th>{{ number_format($totalClosingMrpPrice, 2) }}</th>
                                    </tr>
                                </tfoot> --}}
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

            let selected_batch = '{{ old('batch',request('batch')) }}';

            $('#product').change(function() {
                let product = $(this).val();
                $('#batch').html('<option value="">Select Batch</option>');

                if (product !== '') {
                    $.ajax({
                        method: "GET",
                        url: '{{ route('get_product_batches') }}',
                        data: { product: product }
                    }).done(function(response) {
                        if (response.status) {
                            $.each(response.batches, function(i, item) {
                                if (item.id == selected_batch) {
                                    $('#batch').append('<option value="' + item.id + '" selected>' + 'Batch-' + item.batch_no + '</option>');
                                } else {
                                    $('#batch').append('<option value="' + item.id + '">' + 'Batch-' + item.batch_no + '</option>');
                                }
                            });
                        }
                    });
                }
            });

            // Trigger once in case old('product') exists
            $('#product').trigger('change');

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
