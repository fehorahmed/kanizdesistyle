@extends('layouts.app')
@section('title','AIT Payment List')
@section('style')
    <style>
        .custom-check-box {
            height: calc(1.25rem + 2px);
        }

        .table-bordered thead td, .table-bordered thead th, .table-bordered body td {
            border: 1px solid #000 !important;
            vertical-align: middle;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000000 !important;
        }
        .chalan.table-bordered td, .chalan.table-bordered th {
            border: 1px solid #000 !important;
            vertical-align: middle;
        }

        .form-top {
            position: relative;
        }

        .form-top-table {
            position: absolute;
            right: 0;
            top: 8px;
        }

        ul.code-list {
            list-style: none;
        }

        ul.code-list li {
            min-width: 30px;
            min-height: 30px;
            display: inline;
            border: 1.5px solid black !important;
            margin: 0 5px;
        }

        ul.code-list li {
            display: inline-block;
            min-width: 28px !important;
            height: 28px;
            margin-left: -7px;
            padding: 0;
        }

        ul.code-list {
            float: left;
        }

        .code-area span {
            float: left;
            margin-right: 15px;
        }

        .card-body.form-print-area p {
            color: black !important;
            font-size: 14px;
        }

        .code-area span {
            font-size: 19px;
        }

        .code-area {
            width: 100% !important;
        }
        ul.code-list li {
            text-align: center;
        }
        .payment-list-table td{
            padding: 3px 4px !important;
        }

        }
        @media print {
            @page {
                size: auto;
                margin: 10px 50px !important;
            }
        }
        .payment-list-table td {
            padding: 1px 2px !important;
            font-size: 11px;
        }
        .table.payment-list-table td, .table.payment-list-table th {
            padding: 1px;

    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-body">
                    <form action="{{route('report.ait_payment')}}" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start">Start Date <span class="text-danger">*</span></label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="start_date" name="start_date" value="{{ request()->get('start_date')??date('Y-m-d')  }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end">End Date <span class="text-danger">*</span></label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="end_date" name="end_date" value="{{ request()->get('end_date')??date('Y-m-d')  }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="submit" name="search" class="btn btn-success bg-gradient-success form-control" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(request()->all())
        @if(count($aits) > 0)
            <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <a href="#" onclick="getprint('printArea')" class="btn btn-success btn-sm">Payment List <i
                                class="fa fa-print"></i></a>
                        <a href="#" onclick="getprintChallan('printAreaChallan')" class="btn btn-success btn-sm">Challan Format <i
                                class="fa fa-print"></i></a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive-sm" id="printArea">
                            <div class="row print-heading">
                                <div class="col-12">
                                    <h1 class="text-center m-0" style="font-size: 40px !important;font-weight: bold">
                                        <img height="50px" src="{{ asset('img/logo.png') }}" alt="">
                                        {{ config('app.name') }}
                                    </h1>
                                    <h3 class="text-center m-0" style="font-size: 25px !important;">AIT Payment List</h3>
                                    <h3 class="text-center m-0 mb-2" style="font-size: 19px !important;">Date: {{ request('start_date') }} to {{ request('end_date') }}</h3>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="table" class="table table-bordered payment-list-table">
                                    <thead>
                                    <tr>
                                        <th>S/L</th>
                                        <th>Date</th>
                                        <th>Party</th>
                                        <th>Base Amount</th>
                                        <th>AIT Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($aits as $ait)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $ait->date }}</td>
                                            <td>{{ $ait->supplier->name ?? '' }}</td>
                                            <td>{{ number_format($ait->invoice->base_amount,2) }}</td>
                                            <td>{{ number_format($ait->amount,2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="text-left">
                                            <b>Total Amount = (in word : {{ $inWordOfAmount }} Only.)</b>
                                        </td>
                                        <td class="text-left" colspan=""><b>{{number_format($aits->sum('amount'),2)}}</b></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body form-print-area" id="printAreaChallan">
                        <div class="row">
                            <div class="col-12 text-right" style="">
                                <span>Challan Date: {{ ($aits ? ($aits[0]->date ? date('d-m-Y',strtotime($aits[0]->date)) : '') : '') }}</span>
                                <span>Challan No: 23456</span>
                            </div>
                            <div class="col-12 form-top">
                                <h2  class="text-center">চালান ফরম</h2>
                                <p class="text-center">টি,আর ফরম নং ৬ (এস, আর ৩৭ দ্রষ্টব্যঃ)</p>
                                <table style="width: 223px !important;font-size: 10px; text-align: center;right: 20px;top: 40px;" class="table table-bordered form-top-table">
                                    <tr>
                                        <th style="border-color: #000 !important;">১ম (মূল) কপি</th>
                                        <th style="border-color: #000 !important;">২য় কপি</th>
                                        <th style="border-color: #000 !important;">৩য় কপি</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-2"></div>
                            <div class="col-10">

                                <p>
                                    চালান
                                    নং..................................................................তারিখঃ.............................<br>
                                    বাংলাদেশ ব্যাংক/সোনালী ব্যাংক
                                    লিমিটেডের.....................................জেলার........................... শাখায়
                                    টাকা জমা দেওয়ার চালান
                                </p>

                                <div class="code-area">
                                    <span>কোড নং</span>
                                    <ul class="code-list">
                                        <li>১</li>
                                    </ul>
                                    <ul class="code-list">
                                        <li>১</li>
                                        <li>১</li>
                                        <li>৩</li>
                                        <li>৩</li>
                                    </ul>
                                    <ul class="code-list">
                                        <li>০</li>
                                        <li>০</li>
                                        <li>০</li>
                                        <li>৬</li>
                                    </ul>
                                    <ul class="code-list">
                                        <li>০</li>
                                        <li>৩</li>
                                        <li>১</li>
                                        <li>১</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <table style="font-size: 13px;" class="table chalan table-bordered">
                            <tr>
                                <td class="text-center" colspan="4">জমা প্রদানকারী কর্তৃক পূরণ করিতে হইবে</td>
                                <td width="20%" class="text-center">টাকার অংক</td>
                                <td rowspan="2" class="text-center">বিভাগের নাম এবং চালানের পৃষ্ঠাংকনকারী কর্মকর্তার
                                    নাম, পদবী ও দপ্তর
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">যাহার মারফত প্রদত্ত হইল তাহার নাম ও ঠিকানা</td>
                                <td class="text-center">যে ব্যাক্তির/প্রতিষ্ঠানের পক্ষ হইতে টাকা প্রদত্ত হইল তাহার নাম,
                                    পদবী ও ঠিকানা
                                </td>
                                <td class="text-center">কি বাবদ জমা দেওয়া হইল তাহার বিবরণ</td>
                                <td class="text-center">মুদ্রা ও নোটের বিবরণ/ড্রাফট, পে-অর্ডার ও চেকের বিবরণ</td>
                                <td class="text-center">টাকা</td>

                            </tr>
                            <tr>
                                <td style="" class="text-center">
                                    ভি টি এম ঢাকা, ঢাকা ভ্যাট রেজি নং- ২২২২২২২ এরিয়া কোড নং-২৩৪
                                </td>
                                <td class="text-center">সংযুক্ত তালিকা মোতাবেক</td>
                                <td class="text-center">তৃতীয় পক্ষের বিল হইতে উৎসে কর্তনকৃত ভ্যাট বাবদ অর্থ জমা প্রদান
                                    <br> অর্থবছর <br>

                                    <br>
                                    মাসঃ <br>

                                </td>
                                <td></td>
                                <td style="vertical-align: baseline;" class="text-right"><b>{{ englishToBangla(number_format($aits->sum('amount'),2)) }}</b></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td style="border-top: 1px solid transparent !important;" colspan="4" class="text-right"><b>মোট টাকা</b></td>
                                <td style="vertical-align: baseline;" class="text-right"><b>{{ englishToBangla(number_format($aits->sum('amount'),2)) }}</b></td>
                                <td>.</td>
                            </tr>
                            <tr>
                                <td colspan="4">টাকা(কথায়়) {{ $inWordBangla->bnMoney($aits->sum('amount')) }} মাত্র</td>

                                <td colspan="2" rowspan="3" class="text-center">ম্যানেজার <br> বাংলাদেশ ব্যাংক/সোনালী
                                    ব্যাংক লিমিটেড
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">টাকা পাওয়া গেলো</td>
                            </tr>
                            <tr>
                                <td colspan="4">তারিখঃ</td>
                            </tr>
                        </table>

                        @if(count($aits) > 0)
                            <p style="text-align: right;font-weight: bold;"> -{{ request('checkout_group') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center"><h2>Not found available</h2></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection

@section('script')
    <script>

        $(function (){
            $('#start_date, #end_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });
        })

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {
            document.title = "gas_vat_payment_list_"+'_{{ request('checkout_group') }}';
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
        function getprintChallan(print) {
            document.title = "gas_vat_challan_group_"+'_{{ request('checkout_group') }}';
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
