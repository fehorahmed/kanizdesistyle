@extends('layouts.app')
@section('title','Dashboard')
@section('style')
<style>
    .info-box-1 {
        border-bottom: 30px solid #c2430c;
    }
    .info-box-2 {
        border-bottom: 30px solid #138d75;
    }
    .info-box-3{
        border-bottom: 30px solid #db2065;
    }
    .info-box-4{
        border-bottom: 30px solid #0b51b7;
    }
    .info-box-5 {
        border-bottom: 30px solid #1e3c58;
    }
    .info-box-6 {
        border-bottom: 30px solid #811a9f;
    }
    .info-box-7 {
        border-bottom: 30px solid #3498db;
    }
</style>
@endsection
@section('content')
@if (Auth::user()->role_id==1)
{{-- <div class="row">
    <div class="col-md-8 col-sm-8 col-xs-12 offset-2">
        <div class="info-box">

        </div>
    </div>
</div> --}}
@endif

@if (auth()->user()->can('dashboard'))
    <div class="row justify-content-md-center">

        <div class="col-12 col-md-3">
            <div class="info-box info-box-1">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="text-white fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Sale</span>
                    <span class="info-box-number" id="total_sales">৳{{ number_format($todaySale, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="info-box info-box-2">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="text-white fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Cash Sale</span>
                    <span class="info-box-number">৳{{ number_format($todayCashSale, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="info-box info-box-3">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="text-white fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Due</span>
                    <span class="info-box-number">৳{{ number_format($todayDue, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="info-box info-box-4">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="text-white fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Due Collection</span>
                    <span class="info-box-number">৳{{ number_format($todayDueCollection, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="info-box info-box-5">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="text-white fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Expense</span>
                    <span class="info-box-number">৳{{ number_format($todayExpense, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="info-box info-box-6">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="text-white fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Stock</span>
                    <span class="info-box-number">{{ number_format($totalStock) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="info-box info-box-7">
                <span class="info-box-icon bg-gradient-info elevation-1"><i class="text-white fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock Amount</span>
                    <span class="info-box-number">৳{{ number_format($allTotalStockAmount, 2) }}</span>
                </div>
            </div>
        </div>

    </div>
    {{-- <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Sale</span>
                    <span class="info-box-number">৳{{ number_format($todaySale, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Cash Sale</span>
                    <span class="info-box-number">৳{{ number_format($todayCashSale, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-star-half"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Due</span>
                    <span class="info-box-number">৳{{ number_format($todayDue, 2) }}</span>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Due Collection</span>
                    <span class="info-box-number">৳{{ number_format($todayDueCollection, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Expense</span>
                    <span class="info-box-number">৳{{ number_format($todayExpense, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Stock</span>
                    <span class="info-box-number">{{ number_format($totalStock) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Stock Amount</span>
                    <span class="info-box-number">৳{{ number_format($allTotalStockAmount,2) }}</span>
                </div>
            </div>
        </div>

    </div> --}}
    @if(auth()->user()->role_id == 1)
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Today's Sales Order</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Total</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($todaySaleReceipt as $index=> $receipt)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td><a href="{{ route('sale_receipt.details', ['order' => $receipt->id]) }}">{{ $receipt->order_no }}</a></td>
                                <td>{{ $receipt->customer->name??'' }}</td>
                                <td>৳{{ number_format($receipt->total, 2) }}</td>
                                <td>{{ $receipt->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('pos') }}" class="btn btn-sm btn-info float-left">Place New Order</a>
                    <a href="{{ route('sale_receipt_offline.all') }}" class="btn btn-sm btn-secondary float-right">View All Orders</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Today's Purchase Order</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <td>S/N</td>
                                <th>Order No.</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($todayPurchaseReceipt as $index => $receipt)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td><a href="{{ route('purchase_receipt.details', ['order' => $receipt->id]) }}">{{ $receipt->order_no }}</a></td>
                                    <td>{{ $receipt->supplier->name }}</td>
                                    <td>৳{{ number_format($receipt->total, 2) }}</td>
                                    <td>{{ $receipt->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('purchase_order.create') }}" class="btn btn-sm btn-info float-left">Place New Order</a>
                    <a href="{{ route('purchase_receipt.all') }}" class="btn btn-sm btn-secondary float-right">View All Orders</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Best Selling Products</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Count</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bestSellingProducts as $index => $product)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->count }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('product.add') }}" class="btn btn-sm btn-info float-left">Add New Product</a>
                    <a href="{{ route('product') }}" class="btn btn-sm btn-secondary float-right">View All Products</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Recently Added Products</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($recentlyProducts as $index => $product)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('product.add') }}" class="btn btn-sm btn-info float-left">Add New Product</a>
                    <a href="{{ route('product') }}" class="btn btn-sm btn-secondary float-right">View All Products</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Sales History</h3>

                    <div class="card-tools pull-right">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="card-body p-0" style="">
                    <canvas id="chart-sales-amount" width="100%" height="30"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Order Count History</h3>

                    <div class="card-tools pull-right">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="card-body p-0" style="">
                    <canvas id="chart-order-count" width="100%" height="30"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
    @endif
@endif
@endsection
 @section('script')
 <script src="{{ asset('themes/backend/plugins/chart.js/Chart.bundle.min.js') }}"></script>
 <script>
     var saleAmountLabel = <?php echo $saleAmountLabel; ?>;
     var saleAmount = <?php echo $saleAmount; ?>;
     var orderCount = <?php echo $orderCount; ?>;

     var ctx = document.getElementById('chart-sales-amount');
     var myChart = new Chart(ctx, {
         type: 'line',
         data: {
             labels: saleAmountLabel,
             datasets: [{
                 label: 'Sales Amount',
                 data: saleAmount,
                 backgroundColor: 'rgba(60, 141, 188, 0.2)',
                 borderColor:  'rgba(60,141,188,1)',
                 borderWidth: 1
             }]
         },
         options: {
             scales: {
                 yAxes: [{
                     ticks: {
                         beginAtZero: true
                     }
                 }]
             },
             legend: {
                 display: false,
             },
             tooltips: {
                 displayColors: false,
                 callbacks: {
                     label: function (tooltipItems, data) {
                         return   "৳" + tooltipItems.yLabel;
                     }
                 }
             }
         }
     });

     var ctx2 = document.getElementById("chart-order-count").getContext('2d');
     var myChart2 = new Chart(ctx2, {
         type: 'bar',
         data: {
             labels: saleAmountLabel,
             datasets: [{
                 data: orderCount,
                 backgroundColor: 'rgba(60, 141, 188, 0.2)',
                 borderColor:  'rgba(60,141,188,1)',
                 borderWidth: 1
             }]
         },
         options: {
             scales: {
                 yAxes: [{
                     ticks: {
                         beginAtZero: true
                     }
                 }]
             },
             legend: {
                 display: false,
             },
             tooltips: {
                 displayColors: false
             }
         }
     });
 </script>
@endsection
