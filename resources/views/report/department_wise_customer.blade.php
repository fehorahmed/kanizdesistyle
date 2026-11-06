@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Department Wise Customer')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.department_wise_customer') }}">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control select2" name="department[]" id="department" data-placeholder="All Department" multiple>
                                        {{-- <option value="">All Department</option> --}}
                                        @foreach ($departments as $department)
                                            @if (request('department'))
                                                <option value="{{ $department->id }}" {{ in_array($department->id,request('department'))?'selected':'' }}>{{ $department->name }}</option>
                                            @else
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endif
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
    @if (count($orders) > 0 || count($homeo_orders) > 0 || count($acu_orders) > 0 || count($therapy_orders) > 0 || count($pathology_orders) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Department Wise Customer</h3>
                            @if (request('start_date'))
                                <h6>Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h6>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="display table table-bordered table-striped table-striped-custom" style="width: 100% !important;">
                                <thead>
                                <tr>
                                    <th width="10% !important">Date</th>
                                    <th width="30%">Customer Name</th>
                                    <th width="30%">Customer Mobile</th>
                                    <th width="30%">Address</th>
                                </tr>
                                </thead>
                                @if (request('department') != '')
                                    <tbody>
                                        @if (in_array(1,request('department')) && count($orders) > 0)
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                    <td>{{ $order->customer->name??'' }}</td>
                                                    <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                    <td>{{ $order->customer->address??'' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (in_array(3,request('department')) && count($homeo_orders) > 0)
                                            @foreach($homeo_orders as $order)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                    <td>{{ $order->customer->name??'' }}</td>
                                                    <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                    <td>{{ $order->customer->address??'' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (in_array(4,request('department')) && count($acu_orders) > 0)
                                            @foreach($acu_orders as $order)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                    <td>{{ $order->customer->name??'' }}</td>
                                                    <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                    <td>{{ $order->customer->address??'' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (in_array(5,request('department')) && count($pathology_orders) > 0)
                                            @foreach($pathology_orders as $order)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                    <td>{{ $order->customer->name??'' }}</td>
                                                    <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                    <td>{{ $order->customer->address??'' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (in_array(6,request('department')) && count($therapy_orders) > 0)
                                            @foreach($therapy_orders as $order)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                    <td>{{ $order->customer->name??'' }}</td>
                                                    <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                    <td>{{ $order->customer->address??'' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                    @php
                                        $totalCustomers = count($orders)+count($homeo_orders)+count($acu_orders)+count($therapy_orders)+count($pathology_orders);
                                    @endphp
                                  {{-- <tfoot> --}}
                                    <tr>
                                        <th colspan="" class="text-right">Total Customers</th>
                                        <td>{{ number_format($totalCustomers) }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                   {{-- </tfoot> --}}
                                @else
                                    <tbody>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                <td>{{ $order->customer->name??'' }}</td>
                                                <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                <td>{{ $order->customer->address??'' }}</td>
                                            </tr>
                                        @endforeach
                                        @foreach($homeo_orders as $order)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                <td>{{ $order->customer->name??'' }}</td>
                                                <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                <td>{{ $order->customer->address??'' }}</td>
                                            </tr>
                                        @endforeach
                                        @foreach($acu_orders as $order)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                <td>{{ $order->customer->name??'' }}</td>
                                                <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                <td>{{ $order->customer->address??'' }}</td>
                                            </tr>
                                        @endforeach
                                        @foreach($therapy_orders as $order)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                <td>{{ $order->customer->name??'' }}</td>
                                                <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                <td>{{ $order->customer->address??'' }}</td>
                                            </tr>
                                        @endforeach
                                        @foreach($pathology_orders as $order)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($order->date)->format('d-M-Y') }}</td>
                                                <td>{{ $order->customer->name??'' }}</td>
                                                <td>{{ $order->customer->mobile_no ??'' }}</td>
                                                <td>{{ $order->customer->address??'' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    @php
                                    $totalCustomers = count($orders)+count($homeo_orders)+count($acu_orders)+count($therapy_orders)+count($pathology_orders);
                                @endphp
                              {{-- <tfoot> --}}
                                <tr>
                                    <th colspan="" class="text-right">Total Customers</th>
                                    <td>{{ number_format($totalCustomers) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                               {{-- </tfoot> --}}
                                @endif
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
