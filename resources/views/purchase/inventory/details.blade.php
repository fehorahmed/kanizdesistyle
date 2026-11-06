
@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title')
    Purchase Inventory Details - {{ $product->name }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Filter</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>

                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="date" autocomplete="off">
                                </div>
                                <!-- /.input group -->
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Type</label>

                                <select class="form-control" id="type">
                                    <option value="">All Type</option>
                                    <option value="1">In</option>
                                    <option value="2">Out</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Date</th>
                            {{-- <th>Type</th> --}}
                            <th>Qty(In)</th>
                            <th>Qty(Out)</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Supplier</th>
                            <th>Order</th>
                            <th>Note</th>
                        </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>0</th>
                                <th>0</th>
                                <th colspan="5"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- date-range-picker -->
    <script src="{{ asset('themes/backend/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <script>
        $(function () {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('purchase_inventory.details.datatable') }}",
                    data: function (d) {
                        d.product_id = {{ $product->id }},
                        d.sister_concern_id = {{ $sisterConcern->id }},
                        d.batch_no = {{ $batch }},
                        d.date = $('#date').val(),
                        d.type = $('#type').val()
                    }
                },
                lengthMenu: [
                    [10, 25, 50, -1], 
                    [10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'date', name: 'date'},
                    // {data: 'type', name: 'type'},
                    {data: 'quantity_in', name: 'quantity_in'},
                    {data: 'quantity_out', name: 'quantity_out'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'selling_price', name: 'selling_price'},
                    {data: 'supplier', name: 'supplier.name'},
                    {data: 'order', name: 'order'},
                    {data: 'note', name: 'note'},
                ],
                order: [[ 0, "desc" ]],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ? i : 0;
                    };

                    var totalIn = api
                        .column(1)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var totalOut = api
                        .column(2)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    $(api.column(1).footer()).html('Total: ' + totalIn);
                    $(api.column(2).footer()).html('Total: ' + totalOut);
                }
            });

            //Date range picker
            $('#date').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

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
        })
    </script>
@endsection
