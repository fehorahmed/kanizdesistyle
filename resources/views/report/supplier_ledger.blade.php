@extends('layouts.app')
@section('style')

@endsection
@section('title','Supplier Ledger Report')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.supplier_ledger') }}">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Supplier </label>
                                    <select class="form-control select2" name="supplier" id="supplier">
                                        <option value="">Select Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('supplier')==$supplier->id?'selected':'' }}>{{ $supplier->name }}</option>
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
    @if (count($allData) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Supplier Ledger</h3>
                            @if(request('start_date'))
                                <span><b> Date : </b>{{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            @if($supplierName)
                                <div class="card-header with-border text-center">
                                    <h1 class="card-title" style="font-size: 25px;">Ledger of {{ $supplierName }}</h1>
                                </div>
                            @endif
                            <table class="table table-bordered">
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Particular</th>
                                    <th>Invoice</th>
                                    <th>Payment</th>
                                    <th>Due Balance</th>
                                </tr>
                                @php
                                    $total_due = 0;
                                    $t_invoice = 0;
                                    $t_payment = 0;
                                    $t_due = 0;
                                @endphp

                                @foreach ($allData as $key => $data)
                                    @php
                                        $total_due += $data['invoice'] - $data['return'] - $data['payment'];
                                        $t_invoice += $data['invoice'];
                                        $t_payment += $data['payment'];
                                        $t_due = $total_due;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ date('d-m-Y',strtotime($data['date'])) }}</td>
                                        <td>{{ $data['particular'] }}</td>
                                        <td>{{ $data['invoice'] == 0 ? '' : number_format($data['invoice'], 2) }}</td>
                                        <td>{{ $data['payment'] == 0 ? '' : number_format($data['payment'], 2) }}</td>
                                        <td>{{ number_format($total_due, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong> </td>
                                    <td class="text-end"><strong>{{ number_format($t_invoice, 2) }}</strong> </td>
                                    <td class="text-end"><strong>{{ number_format($t_payment, 2) }} </strong></td>
                                    <td class="text-end"><strong>{{ number_format($t_due, 2) }}</strong> </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    @endif

@endsection

@section('script')
    <script>
        $(function () {
            
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {
            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
