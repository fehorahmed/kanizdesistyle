@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title'.'Sale Report')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.sale.cost.price') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="date" name="date"
                                            value="{{ request()->get('date') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>

                                    <select class="form-control select2" name="customer">
                                        <option value="">All Customer</option>

                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name . ' - ' . $customer->mobile_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Order no.</label>

                                    <input type="text" class="form-control" name="saleId"
                                        value="{{ request()->get('saleId') }}">
                                </div>
                            </div>

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
                                    <label> Department </label>
                                    <select class="form-control select2" name="department">
                                        <option value="">Select Department </option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ request()->get('department') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> User </label>

                                    <select class="form-control select2" name="user">
                                        <option value="">All user </option>

                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request()->get('user') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> Paid /Due </label>

                                    <select class="form-control select2" name="paid_due">
                                        <option value="">Select One </option>
                                        <option value="1" {{ request()->get('paid_due') == 1 ? 'selected' : '' }}>Paid
                                        </option>
                                        <option value="2" {{ request()->get('paid_due') == 2 ? 'selected' : '' }}>Due
                                        </option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> Price </label>
                                    <select class="form-control select2" name="price">
                                        <option value="">Select One </option>
                                        <option value="1" {{ request()->get('price') == 1 ? 'selected' : '' }}>Minimum
                                            Price </option>
                                        <option value="2" {{ request()->get('price') == 2 ? 'selected' : '' }}>Stock
                                            Price </option>

                                    </select>
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order No.</th>
                                    <th>Customer Name</th>
                                    <th>Address</th>


                                    @if (request()->get('price') == 1)
                                        <th>Min Price</th>
                                    @elseif (request()->get('price') == 2)
                                        <th>Stock Price</th>
                                    @endif
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            @php
                                $totalMinimum = 0;
                                $totalStock = 0;
                                $total = 0;
                                $paid = 0;
                                $due = 0;
                            @endphp
                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                        $totalMinimum += $order->minimum_price();
                                        $totalStock += $order->stock_price();
                                        $total += $order->total;
                                         $paid += $order->paid;
                                          $due += $order->due;
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                        <td>{{ $order->order_no }}</td>
                                        <td>{{ $order->customer->name }}</td>
                                        <td>{{ $order->customer->address }}</td>
                                        @if (request()->get('price') == 1)
                                            <td>{{ number_format($order->minimum_price(), 2) }}</td>
                                        @elseif (request()->get('price') == 2)
                                            <td>{{ number_format($order->stock_price(), 2) }}</td>
                                        @endif
                                        <td>{{ number_format($order->total, 2) }}</td>
                                        <td>{{ number_format($order->paid, 2) }}</td>
                                        <td>{{ number_format($order->due, 2) }}</td>
                                        <td><a href="{{ route('sale_receipt.details', ['order' => $order->id]) }}">View
                                                Invoice</a></td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    @if (request()->get('price') == 1)
                                        <td>{{ number_format($totalMinimum, 2) }}</td>
                                    @elseif (request()->get('price') == 2)
                                        <td>{{ number_format($totalStock, 2) }}</td>
                                    @endif

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

    <script>
        $(function() {
            //Initialize Select2 Elements
            $('#table').DataTable();
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
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                table.ajax.reload();
            });

            $('#date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });

            $('#date, #type').change(function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
