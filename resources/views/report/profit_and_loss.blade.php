@extends('layouts.app')
@section('title','Profit & Loss')
@section('style')
    <style>
        .table td,
        .table th {
           font-size: 10px !important;
           padding: 3px !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Filter</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <form action="{{ route('report.profit_and_loss') }}">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control date-picker"
                                                name="start" value="{{ request()->get('start')  }}" autocomplete="off" required>
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
                                        <input type="text" class="form-control date-picker"
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off" required>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

                                    <input class="btn btn-primary form-control" type="submit" value="Submit">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12" style="min-height:300px">
            @if($incomes)
                <section class="card">

                    <div class="card-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                        <div class="adv-table" id="prinarea">
                            <table class="table table-bordered" style="margin-bottom: 0px">
                                <tr>
                                    <th class="text-center">{{ date("F d, Y", strtotime(request()->get('start'))).' - '.date("F d, Y", strtotime(request()->get('end'))) }}</th>
                                </tr>
                            </table>
                            <div style="clear: both">
                                <table class="table table-bordered" style="width:35%; float:left">
                                    <tr>
                                        <th colspan="3" class="text-center">Received(Credit)</th>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <th>Particular</th>
                                        {{-- <th>Payment Type</th>
                                        <th>Bank Details</th> --}}
                                        <th>Amount</th>
                                    </tr>

                                    @foreach($incomes as $income)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($income->date)->format('d-M-Y') }}</td>
                                            <td>{{ $income->particular }}</td>
                                            {{-- <td>
                                                @if($income->transaction_method == 1)
                                                    Cash
                                                @elseif($income->transaction_method == 2)
                                                    Bank
                                                @elseif($income->transaction_method == 3)
                                                    Mobile Banking
                                                @endif
                                            </td>
                                            <td>
                                                @if ($income->transaction_method == 2)
                                                    {{ $income->bank->name.' - '.$income->account->account_no }}
                                                @endif
                                            </td> --}}
                                            <td align="right">{{ $income->amount }}</td>
                                        </tr>
                                    @endforeach

                                    <?php
                                    $incomesCount = count($incomes);
                                    $expensesCount = count($expenses);

                                    if ($incomesCount > $expensesCount)
                                        $maxCount = $incomesCount;
                                    else
                                        $maxCount = $expensesCount;

                                    $maxCount += 2;
                                    ?>

                                    @for($i=count($incomes); $i<$maxCount; $i++)
                                        <tr>
                                            <td><br><br></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor

                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th align="right">{{ number_format($incomes->sum('amount'),2) }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <th>Selling Price</th>
                                    </tr>
                                </table>
                                <table class="table table-bordered" style="width:35%; float:left">
                                    <tr>
                                        <th colspan="3" class="text-center">Payment(Debit)</th>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <th>Particular</th>
                                        {{-- <th width="14%">Payment Type</th>
                                        <th width="18%">Bank Details</th> --}}
                                        <th>Amount</th>
                                    </tr>

                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-M-Y') }}</td>
                                            <td>{{ $expense->particular }}</td>
                                            {{-- <td>
                                                @if($expense->transaction_method == 1)
                                                    Cash
                                                @elseif($expense->transaction_method == 2)
                                                    Bank
                                                @elseif($expense->transaction_method == 3)
                                                    Mobile Banking
                                                @endif
                                            </td>
                                            <td>
                                                @if ($expense->transaction_method == 2)
                                                    {{ $expense->bank->name.' - '.$expense->account->account_no }}
                                                @endif
                                            </td> --}}
                                            <td align="right">{{ $expense->amount }}</td>
                                        </tr>
                                    @endforeach

                                    @for($i=count($expenses); $i<$maxCount; $i++)
                                        <tr>
                                            <td><br><br></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor

                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th align="right">{{ number_format($expenses->sum('amount'),2) }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <th align="right">Cost Price</th>
                                    </tr>
                                </table>
                                <table class="table table-bordered" style="width:30%; float:left">
                                    <tr>
                                        <th colspan="2" class="text-center">Profit</th>
                                    </tr>
                                    <tr>
                                        <th>Revenue </th>
                                        <th>Discount</th>
                                        <th>Vat</th>
                                        <th>Net Profit</th>
                                    </tr>
                                    @php
                                        $totalRevenue = 0;
                                        $totalDiscount = 0;
                                        $totalVat = 0;
                                        $totalNetProfit = 0;
                                    @endphp
                                    @foreach($incomes as $key => $income)
                                        <tr>
                                            <td>{{number_format($income->amount-$expenses[$key]->amount ,2)}}</td>
                                            <td>{{number_format($income->discount_amt,2)}}</td>
                                            <td>{{number_format($income->vat_amount,2)}}</td>
                                            <td>{{number_format($income->amount-$expenses[$key]->amount-$income->discount_amt-$income->vat_amount,2)}}</td>
                                        </tr>
                                        @php
                                            $totalRevenue += $income->amount-$expenses[$key]->amount;
                                            $totalDiscount += $income->discount_amt;
                                            $totalVat += $income->vat_amount;
                                            $totalNetProfit += ($income->amount-$expenses[$key]->amount-$income->discount_amt-$income->vat_amount);
                                        @endphp
                                    @endforeach

                                    @for($i=count($incomes); $i<$maxCount; $i++)
                                        <tr>
                                            <td><br><br></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor

                                    <tr>
                                        <th>{{ number_format($totalRevenue,2) }}</th>
                                        <th>{{ number_format($totalDiscount,2) }}</th>
                                        <th>{{ number_format($totalVat,2) }}</th>
                                        <th align="right">{{ number_format($totalNetProfit,2) }}</th>
                                    </tr>

                                    <tr>
                                        <th>Revenue</th>
                                        <th>Discount</th>
                                        <th>Vat</th>
                                        <th>Net Profit</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>

    @if($incomes)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Summary</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <strong>Profit: </strong> {{ number_format($totalNetProfit,2) }}
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $(function () {
            //Date picker
            // $('#start, #end').datepicker({
            //     autoclose: true,
            //     format: 'yyyy-mm-dd'
            // });
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
