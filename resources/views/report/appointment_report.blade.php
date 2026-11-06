@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Appointment Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.appointment_report') }}">
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

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Doctor</label>
                                    <select class="form-control select2" name="doctor" id="doctor">
                                        <option value="">Select Option</option>
                                        @foreach ($doctors as $doctor)
                                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Appointment Type</label>
                                    <select class="form-control select2" name="appointment_type" id="appointment_type">
                                        <option value="">Select Option</option>
                                        <option value="Online">Online</option>
                                        <option value="Offline">Offline</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Payment Status</label>
                                    <select class="form-control select2" name="is_payment" id="is_payment">
                                        <option value="">Select Option</option>
                                        <option value="1">Complete</option>
                                        <option value="0">Incomplete</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prescription Status</label>
                                    <select class="form-control select2" name="is_prescription" id="is_prescription">
                                        <option value="">Select Option</option>
                                        <option value="1">Complete</option>
                                        <option value="0">Incomplete</option>
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
    {{-- @if (count($payments) > 0 || count($homeo_payments) > 0 || count($acu_payments) > 0 || count($therapy_payments) > 0 || count($pathology_payments) > 0) --}}
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Appointment Report</h3>
                            @if (request('start_date'))
                                <h6>Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h6>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped" style="width:100% !important;">
                                <thead>
                                <tr>
                                    <th>Apt No</th>
                                    <th>Doctor</th>
                                    <th>Patient</th>
                                    <th>Patient Type</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Time</th>
                                    <th>Appointment Fee</th>
                                    <th>Appointment Type</th>
                                    <th>Payment Status</th>
                                    <th>Prescription Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->apt_no }}</td>
                                            <td>{{ $appointment->doctor->name??'' }}</td>
                                            <td>{{ $appointment->patient->name??'' }}</td>
                                            <td>{{ $appointment->patientTypeFee->parent->name??'' }}</td>
                                            <td>{{ date('d-m-Y',strtotime($appointment->appointment_date)) }}</td>
                                            <td>{{ date('h:i A',strtotime($appointment->appointment_time)) }}</td>
                                            <td>{{ number_format($appointment->appointment_fee,2) }}</td>
                                            <td>
                                                @if($appointment->appointment_type == 'Online')
                                                    <span class="badge badge-success">{{ $appointment->appointment_type }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ $appointment->appointment_type }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($appointment->is_payment == 1)
                                                    <span class="badge badge-success">Complete</span>
                                                @else
                                                    <span class="badge badge-danger">Incomplete</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($appointment->is_prescription == 1)
                                                    <span class="badge badge-success">Complete</span>
                                                @else
                                                    <span class="badge badge-danger">Incomplete</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot> --}}
                                    <tr>
                                        <th class="text-right" colspan="6"><strong>Total</strong></th>
                                        <th><strong>{{ number_format($appointments->sum('appointment_fee'),2) }}</strong></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                {{-- </tfoot> --}}
                            </table>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    {{-- @endif --}}

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
