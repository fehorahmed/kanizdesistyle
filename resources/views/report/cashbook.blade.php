@extends('layouts.app')
@section('style')

@endsection
@section('title','Cashbook')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.cashbook') }}">
                        <input type="hidden" name="is_request" value="1">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('start_date') ?? date('d-m-Y') }}" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" autocomplete="off" class="form-control date-picker" value="{{ request('end_date') ?? date('d-m-Y') }}" id="end_date" name="end_date">
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
    @if (count($result) > 0)
        <div class="row">
            <div class="col-sm-12">
                <section class="card">
                    <div class="card-header">
                        <a href="#" role="button" onclick="getprint('printArea')" class="btn btn-primary bg-gradient-primary btn-sm">Print <i class="fa fa-print"></i></a>
                    </div>
                    <div class="card-body" id="printArea">
                        <div class="header text-center">
                            <h3>Cashbook</h3>
                            @if(request('start_date'))
                                <span><b> Date : </b>{{ date('d-m-Y',strtotime(request('start_date'))) }} to {{ date('d-m-Y',strtotime(request('end_date'))) }}</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Date</th>
                                    <th>Particular</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                                @foreach ($result as $key => $data)
                                    <tr>
                                        <td>{{ date('d-m-Y',strtotime($data['date'])) }}</td>
                                        <td>{{ $data['particular'] }}</td>
                                        <td>{{ $data['debit'] }}</td>
                                        <td>{{ $data['credit'] }}</td>
                                        <td class="text-end">{{ number_format($data['balance'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total</strong></td>
                                    <td>{{ $metaData['total_debit'] }}</td>
                                    <td>{{ $metaData['total_credit'] }}</td>
                                    <td class="text-end">{{ number_format($data['balance'],2) }}</td>
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
