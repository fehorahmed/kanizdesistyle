@extends('layouts.app')
@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection
@section('title','Pathology Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.pathology_report') }}">
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
                            {{-- <div class="col-md-2">
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
                            <h3>Pathology Report</h3>
                            @if (request('start_date'))
                                <h6>Date: {{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</h6>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped" style="width:100% !important;">
                                <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Date</th>
                                    <th>Order No</th>
                                    <th>Order Total Amount</th>
                                    <th>IMC Tests</th>
                                    <th>IMC Total Cost Price</th>
                                    <th>IMC Total Sale Price</th>
                                    <th>IMC Discount(%)</th>
                                    <th>IMC Commission(%)</th>
                                    <th>IMC Gross Profit</th>
                                    <th>IMC Net Profit</th>
                                    <th>DMFR Tests</th>
                                    <th>DMFR Total Cost Price</th>
                                    <th>DMFR Total Sale Price</th>
                                    <th>DMFR Discount(%)</th>
                                    <th>DMFR Commission(%)</th>
                                    <th>DMFR Gross Profit</th>
                                    <th>DMFR Net Profit</th>
                                    <th>Total Discount(%)</th>
                                    <th>Total Commission(%)</th>
                                    <th>Gross Profit(IMC+DMFR)</th>
                                    <th>Net Profit(IMC+DMFR)</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grand_total_imc_cost = 0;
                                        $grand_total_imc_sale = 0;
                                        $grand_total_imc_discount = 0;
                                        $grand_total_imc_commission = 0;
                                        $grand_total_imc_net_profit = 0;
                                        $grand_total_dmfr_cost = 0;
                                        $grand_total_dmfr_sale = 0;
                                        $grand_total_dmfr_discount = 0;
                                        $grand_total_dmfr_commission = 0;
                                        $grand_total_service_charge_sale = 0;
                                        $grand_total_service_charge_cost = 0;
                                        $grand_total_gross_profit = 0;
                                        $grand_total_net_profit = 0;
                                    @endphp
                                    @foreach ($services as $index => $order)
                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            <td>{{ date('d-m-Y',strtotime($order->date)) }}</td>
                                            <td>{{ $order->order_no }}</td>
                                            <td>{{ $order->total }}</td>
                                            @php
                                                $imc_total_cost = 0;
                                                $imc_total_sale = 0;
                                                $dmfr_total_cost = 0;
                                                $dmfr_total_sale = 0;
                                                $total_service_charge_cost = 0;
                                                $total_service_charge_sale = 0;
                                                $total_service_charge_gross_profit = 0;
                                                $serviceDetails = \App\Models\PathologyServiceDetails::with('service')->where('pathology_sale_service_order_id',$order->id)->get();
                                            @endphp
                                            <td>
                                                <ul style="padding: 0px;list-style: none;">
                                                    @foreach ($serviceDetails as $serviceDetail)
                                                        @if($serviceDetail->service->pathology_test_center == 'IMC' && !in_array($serviceDetail->service->id,[73,151,175]))
                                                            @php
                                                                $imc_total_cost += $serviceDetail->cost_price;
                                                                $imc_total_sale += $serviceDetail->selling_price;
                                                            @endphp
                                                            <li><i class="fas fa-check-double text-success"></i> {{ $serviceDetail->service->name }} (C.S - {{ $serviceDetail->cost_price }}, S.P - {{ $serviceDetail->selling_price }})</li>
                                                        @endif
                                                        @if (in_array($serviceDetail->service->id,[73,151,175]))
                                                            @php
                                                                $total_service_charge_cost += $serviceDetail->cost_price;
                                                                $total_service_charge_sale += $serviceDetail->selling_price;
                                                            @endphp
                                                            <li><i class="fas fa-check-double text-success"></i> {{ $serviceDetail->service->name }} (C.S - {{ $serviceDetail->cost_price }}, S.P - {{ $serviceDetail->selling_price }})</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>{{ $imc_total_cost+$total_service_charge_cost }}</td>
                                            <td>{{ $imc_total_sale+$total_service_charge_sale }}</td>
                                            @php
                                                $imc_discount = ($imc_total_sale+$total_service_charge_sale) * ($order->discount_percent ?? 0) / 100;
                                                $imc_commission = ($imc_total_sale+$total_service_charge_sale) * ($order->commission_percent ?? 0) / 100;
                                            @endphp
                                            <td>{{ $imc_discount }} ({{ $order->discount_percent ?? 0 }}%)</td>
                                            <td>{{ $imc_commission }} ({{ $order->commission_percent ?? 0 }}%)</td>
                                            <td>{{ ($imc_total_sale+$total_service_charge_sale) - ($imc_total_cost+$total_service_charge_cost) }}</td>
                                            <td>{{ ($imc_total_sale+$total_service_charge_sale) - ($imc_total_cost+$total_service_charge_cost)-$imc_discount-$imc_commission }}</td>
                                            <td>
                                                <ul style="padding: 0;list-style: none;">
                                                    @foreach ($serviceDetails as $serviceDetail)
                                                        @if ($serviceDetail->service->pathology_test_center == 'DMFR' && !in_array($serviceDetail->service->id,[73,151,175]))
                                                            @php
                                                                $dmfr_total_cost += $serviceDetail->cost_price;
                                                                $dmfr_total_sale += $serviceDetail->selling_price;
                                                            @endphp    
                                                            <li><i class="fas fa-check-double text-success"></i> {{ $serviceDetail->service->name }} (C.S - {{ $serviceDetail->cost_price }}, S.P - {{ $serviceDetail->selling_price }})</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>{{ $dmfr_total_cost }}</td>
                                            <td>{{ $dmfr_total_sale }}</td>
                                            @php
                                                $dmfr_discount = ($dmfr_total_sale) * ($order->discount_percent ?? 0) / 100;
                                                $dmfr_commission = ($dmfr_total_sale) * ($order->commission_percent ?? 0) / 100;
                                            @endphp
                                            <td>{{ $dmfr_discount }} ({{ $order->discount_percent ?? 0 }}%)</td>
                                            <td>{{ $dmfr_commission }} ({{ $order->commission_percent ?? 0 }}%)</td>
                                            <td>{{ $dmfr_total_sale-$dmfr_total_cost }}</td>
                                            <td>{{ $dmfr_total_sale-$dmfr_total_cost-$dmfr_discount-$dmfr_commission }}</td>
                                            <td>{{ $order->discount }} ({{ $order->discount_percent ?? 0 }}%)</td>
                                            <td>{{ $order->commission }} ({{ $order->commission_percent ?? 0 }}%)</td>
                                            <td>{{ ($imc_total_sale-$imc_total_cost)+($dmfr_total_sale-$dmfr_total_cost)+($total_service_charge_sale-$total_service_charge_cost) }}</td>
                                            @php
                                                $net_profit = (($imc_total_sale-$imc_total_cost)+($dmfr_total_sale-$dmfr_total_cost)+($total_service_charge_sale-$total_service_charge_cost))-$order->discount-$order->commission;
                                            @endphp
                                            <td>{{ $net_profit }}</td>
                                        </tr>
                                        @php
                                            $grand_total_imc_cost += $imc_total_cost;
                                            $grand_total_imc_sale += $imc_total_sale;
                                            $grand_total_imc_discount += $imc_discount;
                                            $grand_total_imc_commission += $imc_commission;
                                            $grand_total_imc_net_profit += ($imc_total_sale+$total_service_charge_sale) - ($imc_total_cost+$total_service_charge_cost)-$imc_discount-$imc_commission;
                                            $grand_total_dmfr_cost += $dmfr_total_cost;
                                            $grand_total_dmfr_sale += $dmfr_total_sale;
                                            $grand_total_dmfr_discount += $dmfr_discount;
                                            $grand_total_dmfr_commission += $dmfr_commission;
                                            $grand_total_service_charge_sale += $total_service_charge_sale;
                                            $grand_total_service_charge_cost += $total_service_charge_cost;
                                            $grand_total_gross_profit += ($imc_total_sale-$imc_total_cost)+($dmfr_total_sale-$dmfr_total_cost)+($total_service_charge_sale-$total_service_charge_cost);
                                            $grand_total_net_profit += $net_profit;
                                            $total_service_charge_gross_profit += $total_service_charge_sale-$total_service_charge_cost;
                                        @endphp
                                    @endforeach
                                </tbody>
                                {{-- <tfoot> --}}
                                    <tr>
                                        <th class="text-right" colspan="3"><strong>Total</strong></th>
                                        <th><strong>{{ $services->sum('total') }}</strong></th>
                                        <th></th>
                                        <th><strong>{{ $grand_total_imc_cost+$grand_total_service_charge_cost }}</strong></th>
                                        <th><strong>{{ $grand_total_imc_sale+$grand_total_service_charge_sale }}</strong></th>
                                        <th><strong>{{ $grand_total_imc_discount }}</strong></th>
                                        <th><strong>{{ $grand_total_imc_commission}}</strong></th>
                                        <th><strong>{{ $grand_total_imc_sale+$grand_total_service_charge_sale-($grand_total_imc_cost+$grand_total_service_charge_cost) }}</strong></th>
                                        <th><strong>{{ $grand_total_imc_net_profit }}</strong></th>
                                        <th></th>
                                        <th><strong>{{ $grand_total_dmfr_cost }}</strong></th>
                                        <th><strong>{{ $grand_total_dmfr_sale }}</strong></th>
                                        <th><strong>{{ $grand_total_dmfr_discount }}</strong></th>
                                        <th><strong>{{ $grand_total_dmfr_commission }}</strong></th>
                                        <th><strong>{{ $grand_total_dmfr_sale-$grand_total_dmfr_cost }}</strong></th>
                                        <th><strong>{{ $grand_total_dmfr_sale-$grand_total_dmfr_cost-$grand_total_dmfr_discount-$grand_total_dmfr_commission }}</strong></th>
                                        <th><strong>{{ $services->sum('discount') }}</strong></th>
                                        <th><strong>{{ $services->sum('commission') }}</strong></th>
                                        <th><strong>{{ $grand_total_gross_profit }}</strong></th>
                                        <th><strong>{{ $grand_total_net_profit }}</strong></th> 
                                        
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
