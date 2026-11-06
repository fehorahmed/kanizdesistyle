@extends('layouts.app')
@section('title'.'Sale Report')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.4/css/fixedHeader.dataTables.min.css">
<style>
    @media print {
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black !important;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        /* Optional: Hide any unwanted elements during print */
        .btn, .input-group-addon, .select2, #start, #end {
            display: none !important;
        }

    }
</style>


@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('selling_product_report') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Product</label>

                                    <select class="form-control select2" name="product">
                                        <option value="">All Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ request()->get('product') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start') ?? date('m/d/Y') }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="end" name="end" value="{{ request()->get('end') ?? date('m/d/Y') }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> &nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <section class="card">
                <div class="card-header">
                    <a href="#" role="button" onclick="getprint('table')"
                    class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="display table table-bordered table-striped table-striped-custom">
                            <thead>
                                {{-- <tr>
                                    <td colspan="14">
                                        <h1 style="margin: 0;">Integrated Medicare Ltd.</h1>
                                        <h6 style="font-size: 15px;margin: 0;">Diagnostic and Consultation Centre</h6>
                                        <p style="margin: 0;font-weight: normal;">178-179,Two Star Tower, IFIC Building(1st & 8th Floor)<br/>
                                            East Tejturi Bazar, Farmgate, Tejgaon, Dhaka-1215</p>
                                    </td>
                                    <td colspan="4">
                                        <img style="height: 120px; margin: 0" src="{{ asset('img/logo.jpg') }}" alt="">
                                    </td>
                                </tr> --}}

                                <tr>
                                    <th>S/L</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Cost Cost</th>
                                    <th>Selling Price</th>
                                    <th>Offline Sold</th>
                                    <th>Online Sold</th>
                                    <th>Total Sold Qty</th>
                                    <th>Total Cost</th>
                                    <th>MRP Total</th>
                                    <th>Discount Amt</th>
                                    <th>Discount Percent(%)</th>
                                    <th>Return Amt</th>
                                    <th>Total D&R Amt</th>
                                    <th>Receivable Amt</th>
                                    <th>Total Profit</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $totalProfit = 0;
                                    $mrpTotal = 0;
                                    $discountTotal = 0;
                                    $offlineTotal = 0;
                                    $onlineTotal = 0;
                                    $soldTotal = 0;
                                    $totalCost = 0;
                                    $totalDR = 0;
                                    $receiveableTotal = 0;
                                @endphp
                                @foreach($inventories as $key=>$inventory)
                                <tr style="{{ $key % 2 === 0 ? 'background-color: white !important' : 'background-color: #e9ecef !important' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $inventory->product->name ?? '' }}</td>
                                    <td>{{ $inventory->product->category->name ?? '' }}</td>
                                    <td>{{ number_format($inventory->avg_cost_price, 2) }}</td>
                                    <td>{{ number_format($inventory->avg_selling_price, 2) }}</td>
                                    <td>{{ number_format($inventory->offline_quantity, 2) }}</td>
                                    <td>{{ number_format($inventory->online_quantity, 2) }}</td>
                                    <td>{{ number_format($inventory->total_quantity, 2) }}</td>
                                    <td>{{ number_format($inventory->total_cost, 2) }}</td>
                                    <td>{{ number_format($inventory->total_sales, 2) }}</td>
                                    <td>{{ number_format($inventory->total_discount, 2) }}</td>
                                    <td>{{ number_format(($inventory->total_discount*100)/$inventory->total_sales) }}%</td>
                                    <td>0.00</td>
                                    <td>{{ number_format($inventory->total_discount, 2) }}</td>
                                    <td>{{ number_format($inventory->total_sales - $inventory->total_discount, 2) }}</td>
                                    <td>{{ number_format($inventory->profit - $inventory->total_discount, 2) }}</td>
                                </tr>
                                @php
                                    $totalProfit += $inventory->profit - $inventory->total_discount;
                                    $mrpTotal += $inventory->total_sales;
                                    $offlineTotal += $inventory->offline_quantity;
                                    $onlineTotal += $inventory->online_quantity;
                                    $soldTotal += $inventory->total_quantity;
                                    $totalCost += $inventory->total_cost;
                                    $totalDR += $inventory->total_discount;
                                    $discountTotal += $inventory->total_discount;
                                    $receiveableTotal += $inventory->total_sales - $inventory->total_discount;
                                @endphp
                                @endforeach

                            </tbody>

                            {{-- <tfoot> --}}
                                <tr>
                                    <th class="text-right" colspan="5">Total</th>
                                    <th>{{$offlineTotal}}</th>
                                    <th>{{$onlineTotal}}</th>
                                    <th>{{ $soldTotal }}</th>
                                    <th>৳{{ number_format($totalCost, 2)}}</th>
                                    <th>৳{{ number_format($mrpTotal, 2)}}</th>
                                    <th>৳{{ number_format($discountTotal, 2)}}</th>
                                    <th></th>
                                    <th></th>
                                    <th>৳{{ number_format($totalDR, 2)}}</th>
                                    <th>৳{{ number_format($receiveableTotal, 2)}}</th>
                                    <th>৳{{ number_format($totalProfit, 2)}}</th>
                                </tr>
                            {{-- </tfoot> --}}
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
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
        $('.select2').select2();
        //Date picker
        $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
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
