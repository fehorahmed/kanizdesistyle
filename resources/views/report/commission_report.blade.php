@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Pathology Commission Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.commission_report') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('start_date',date('d-m-Y')) }}" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('end_date',date('d-m-Y')) }}" id="end_date" name="end_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Referar</label>
                                    <select class="form-control select2" name="referar" id="referar">
                                        <option value="All" {{ request('referar')=='All'?'selected':'' }}>All</option>
                                        @foreach ($referars as $referar)
                                            <option value="{{ $referar->id }}" {{ request('referar')==$referar->id?'selected':'' }}>{{ $referar->name }} | {{ $referar->id_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-2">
                                <div class="form-group">
                                    <label>Payment Status</label>
                                    <select class="form-control select2" name="is_payment" id="is_payment">
                                        <option value="">Select Option</option>
                                        <option value="1">Complete</option>
                                        <option value="0">Incomplete</option>
                                    </select>
                                </div>
                            </div> --}}
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
    @if (count($services) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Pathology Commission Report</h3>
                            @if (request('start_date'))
                                <h6>Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h6>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped" style="width:100% !important;">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Referar Name</th>
                                    <th>Referar ID</th>
                                    <th>Order Date</th>
                                    <th>Order No</th>
                                    <th>Order Total</th>
                                    <th>Commission(%)</th>
                                    <th class="text-right">Commission</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $index => $service)
                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            <td>{{ $service->referar->name??'' }}</td>
                                            <td>{{ $service->referar->id_no??'' }}</td>
                                            <td>{{ $service->date }}</td>
                                            <td><a target="_blank" href="{{ route('pathology_sale_service_receipt',['pathologySaleService'=>$service->id]) }}">{{ $service->order_no }}</a></td>
                                            <td>{{ number_format($service->sub_total,2) }}</td>
                                            <td>{{ $service->commission_percent }}%</td>
                                            <td class="text-right">{{ number_format($service->commission,2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot> --}}
                                    <tr>
                                        <th class="text-right" colspan="7"><strong>Total</strong></th>
                                        <th class="text-right"><strong>{{ number_format($services->sum('commission'),2) }}</strong></th>                                      
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
