@extends('layouts.app')
@section('title','Vat Certificate 6.6')
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
        @media print {
            @page {
                size: auto;
                margin: .2in !important;
            }
        }
        .payment-list-table td {
            padding: 1px 2px !important;
            font-size: 11px;
        }
        .govt-logo{
            position: absolute;
            width: 223px;
            left: 0;
            top: 0;
            text-align: center;
        }
        .table-bordered.form-certificate-table thead td, .table-bordered.form-certificate-table thead th, .table-bordered.form-certificate-table body td {
            border: 1px solid #000 !important;
            vertical-align: middle;
            padding: 2px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{route('vat_certificate_six_point_six')}}" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Start Date</label>
                                    <input type="text" class="form-control date-picker" id="start" name="start" value="{{ request()->get('start')??date('d-m-Y')  }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end">End Date</label>
                                    <input type="text" class="form-control date-picker" id="end" name="end" value="{{ request()->get('end')??date('d-m-Y')  }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('party') ? 'has-error' :'' }}">
                                    <label for="party">Party <span class="text-danger">*</span></label>
                                    <select required class="form-control select2" id="party" style="width: 100%;"
                                            name="party" data-placeholder="Search Party">
                                        <option value="">Search Party</option>
                                        @foreach ($parties as $party)
                                            <option value="{{ $party->id }}" {{ request('party') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="submit" name="search" class="btn btn-primary bg-gradient-primary form-control"
                                           value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    @if(request()->all())
        @if(count($vats) > 0)
            <div class="row" style="background-color: white !important;">
                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <a href="#" onclick="getprint('printArea')" class="btn btn-primary btn-sm">Print</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive" id="printArea">
                                <div class="row">
                                    <div class="col-12 form-top mb-1" style="position: relative">
                                        <div class="govt-logo">
                                            <img style="width: 75px;" src="{{ asset('uploads/gvt.png') }}" alt="">
                                        </div>
                                        <h3  class="text-center">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</h3>
                                        <h5 class="text-center">জাতীয় রাজস্ব বোর্ড</h5>
                                        <h5 class="text-center">উৎসে কর কর্তন সনদপত্র</h5>
                                        <h6 class="text-center">বিধি ৪০ এর উপ-বিধি (১) এর দফা (চ) দ্রস্টব্য</h6>
                                        <table style="width: 223px !important;font-size: 10px; text-align: center;right: 10px;top: 40px;" class="table table-bordered form-top-table">
                                            <tr>
                                                <th style="border-color: #fff !important;"><h5 style="margin: 0">মূসক - ৬.৬</h5></th>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-12">
                                        <table style="width: 100% !important;" class="table table-bordered">
                                            <tr>
                                                <td width="40%" style="border-color: #fff !important;padding: 2px 0;font-size: 16px">উৎসে কর কর্তনকারী সত্তার নাম</td>
                                                <td width="2%" style="border-color: #fff !important;padding: 2px 0;font-size: 16px">:</td>
                                                <td style="border-color: #fff !important;padding: 2px 0;font-size: 16px">ভি টি এম</td>
                                            </tr>
                                            <tr>
                                                <td style="border-color: #fff !important;padding: 2px 0;font-size: 16px">উৎসে কর কর্তনকারী সত্তার ঠিকানা</td>
                                                <td style="border-color: #fff !important;padding: 2px 0;font-size: 16px">:</td>
                                                <td style="border-color: #fff !important;padding: 2px 0;font-size: 16px"> ঢাকা,বাংলাদেশ</td>
                                            </tr>
                                            <tr>
                                                <td style="border-color: #fff !important;padding: 2px 0;font-size: 16px">উৎসে কর কর্তনকারী সত্তার বি আই এন  (প্রযোজ্য ক্ষেত্রে)</td>
                                                <td style="border-color: #fff !important;padding: 2px 0;font-size: 16px">:</td>
                                                <td style="border-color: #fff !important;padding: 2px 0;font-size: 16px">০০০০০০৪৫১-০০০৩</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <p>উৎসে কর কর্তন সনদপত্র নং : ১২৩৪৫</p>
                                    </div>
                                    <div class="col-6 text-right">
                                        <p>জারির তারিখঃ {{ englishToBangla(date('d-m-Y')) }}</p>
                                    </div>
                                    <div class="col-12">
                                        <p>এই মর্মে প্রত্যয়ন করা যাইতেছে যে, আইনের ধারা ৪৯ অনুযায়ী উৎসে কর কর্তনযোগ্য সরবরাহ হইতে প্রযোজ্য মূল্য সংযোজন কর বাবদ উৎসে কর কর্তন করা হইল। কর্তনকৃত মূল্য সংযোজন করের অর্থ বুক ট্রান্সফার / ট্রেজারি চালান / দাখিলপ্ত্রে বৃদ্ধিকারী সমন্বয়ের মাধ্যমে সরকারি কোষাগারে জমা প্রদান করা হইয়াছে। কপি এতদসংগে সংযুক্ত করা হইল (প্রযোজ্য ক্ষেত্রে)।</p>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                                <div class="">
                                    <table class="table table-bordered form-certificate-table">
                                        <thead>
                                        <tr>
                                            <td rowspan="2" class="text-center">ক্রমিক নং</td>
                                            <td colspan="2" class="text-center">সরবরাহকারীর</td>
                                            <td colspan="2" class="text-center">সংশ্লিস্ট কর চালানপত্র</td>
                                            <td rowspan="2" class="text-center">মোট সরবরাহ মূল্য <span class="text-danger">১</span> (টাকায়)</td>
                                            <td rowspan="2" class="text-center">মূসকের পরিমাণ (টাকা)</td>
                                            <td rowspan="2" class="text-center">উৎসে কর্তনকৃত মূসকের পরিমাণ (টাকা) </td>
                                            <td class="extra_column"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">নাম</td>
                                            <td class="text-center">বি আই এন</td>
                                            <td class="text-center">নম্বর</td>
                                            <td class="text-center">ইস্যুর তারিখ</td>
                                            <td class="extra_column"></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $totalBaseAmount = 0;
                                        ?>
                                        @foreach($vats as $vat)
                                            <tr>
                                                <td class="text-center">{{ englishToBangla($loop->iteration) }}</td>
                                                <td class="text-center">{{$vat->customer->name ?? ''}}</td>
                                                <td class="text-center">২৩৪৫৬</td>
                                                <td class="text-center">
                                                        ৪৫৩৪৫
                                                        ,১২/১২/২০২৩
                                                        (৫৪৫৩২)
                                                </td>
                                                <td class="text-center">{{ englishToBangla(\Carbon\Carbon::parse($vat->date)->format('d-m-Y'))}}</td>
                                                <td class="text-center">{{ englishToBangla(number_format($vat->invoice->total_amount,2)) }}</td>
                                                <td class="text-center">{{ englishToBangla(number_format($vat->amount,2)) }}</td>
                                                <td class="text-center">{{ englishToBangla(number_format($vat->amount,2)) }}</td>
                                                <td class="extra_column">
                                                    @if(\File::exists(public_path($vat->vat_document)))
                                                        <a href="{{ asset($vat->vat_document) }}" download class="btn btn-primary btn-sm extra_column">Pdf</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php
                                            $totalBaseAmount += $vat->invoice->sub_total ?? 0;
                                            ?>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">সর্বমোট</th>

                                            <th class="text-right"
                                                colspan="">{{englishToBangla(number_format($totalBaseAmount,2))}}</th>
                                            <th class="text-right"
                                                colspan="">{{englishToBangla(number_format($vats->sum('amount'),2))}}</th>
                                            <th class="text-right"
                                                colspan="">{{englishToBangla(number_format($vats->sum('amount'),2))}}</th>
                                            <th class="extra_column"></th>
                                        </tr>
                                        </tfoot>

                                    </table>
                                </div>
                                <div class="row" style="margin-top: 30px">
                                    <div class="col-12">
                                        <p>ক্ষমতাপ্রাপ্ত কর্মকর্তার – </p>
                                        <p>স্বাক্ষর: </p>
                                        <p>নাম: </p>
                                        <p><small><span class="text-danger">১</span> মূল্য ও সম্পূরক শুল্ক (যদি থাকে) সহ মূল্য ।</small> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
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
        $(function () {
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });
            $("#checkAll").click(function () {
                $('input:checkbox').not(this).prop('disabled', this.disabled);
                $('input:checkbox').not(this).prop('checked', this.checked);

                init();
            });
        })

        function int() {
            $('.vat_id').attr("disabled", true);
        }

        var APP_URL = '{!! url()->full()  !!}';

        function getprint(print) {
            $('.print-heading').css('display', 'block');
            $('.extra_column').remove();
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
