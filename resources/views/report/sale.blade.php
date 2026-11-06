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
                    <form action="{{ route('report.sale') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="date" name="date" value="{{ request()->get('date')  }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>

                                    <select class="form-control select2" name="customer">
                                        <option value="">All Customer</option>

                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name.' - '.$customer->mobile_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Order no.</label>

                                    <input type="text" class="form-control" name="saleId" value="{{ request()->get('saleId') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Product</label>

                                    <select class="form-control select2" name="product">
                                        <option value="">All Product</option>

                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ request()->get('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> User </label>

                                    <select class="form-control select2" name="user">
                                        <option value="">All user </option>

                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request()->get('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Paid /Due </label>

                                    <select class="form-control select2" name="paid_due">
                                        <option value="">Select One </option>
                                         <option value="1" {{ request()->get('paid_due') == 1 ? 'selected' : '' }}>Paid</option>
                                          <option value="2" {{ request()->get('paid_due') == 2 ? 'selected' : '' }}>Due</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sale Type </label>
                                    <select class="form-control select2" name="sale_type">
                                        <option value="">Select Option </option>
                                        <option value="1" {{ request('sale_type')==1?'selected':'' }}>Offline </option>
                                        <option value="2" {{ request('sale_type')==2?'selected':'' }}>Online </option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-3">
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

    <div class="row">
        <div class="col-sm-12">
            <section class="card">
                <div class="card-header">
                    <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                </div>
                <div class="card-body" id="printArea">
                    <div class="header text-center">
                        <h3>Functional Sale Report</h3>
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
                                {{-- @if ()
                                     <th>Min Price</th>
                                @elseif ()
                                     <th>Cost Price</th>
                                @endif --}}
                                <th>SubTotal</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                    <td>{{ $order->order_no }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->customer->address }}</td>
                                    <td>{{ number_format($order->sub_total, 2) }}</td>
                                    <td>{{ number_format($order->discount, 2) }}</td>
                                    <td>{{ number_format($order->total, 2) }}</td>
                                    <td>{{ number_format($order->paid, 2) }}</td>
                                    <td>{{ number_format($order->due, 2) }}</td>
                                    <td><a href="{{ route('sale_receipt.details', ['order' => $order->id]) }}">View Invoice</a></td>
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <td>{{ number_format($orders->sum('sub_total'), 2) }}</td>
                                <td>{{ number_format($orders->sum('discount'), 2) }}</td>
                                <td>{{ number_format($total, 2) }}</td>
                                <td>{{ number_format($paid, 2) }}</td>
                                <td>{{ number_format($due, 2) }}</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>

                        {{-- {{ $orders->appends($appends)->links() }} --}}
                    </div>
                </div>
            </section>
        </div>
    </div>
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
