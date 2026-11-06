@extends('layouts.app')
@section('title','AIT Chalan')
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
                    <form action="{{route('report.ait_chalan')}}" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start">Start Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="start" name="start" value="{{ request()->get('start')??date('Y-m-d')  }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end">End Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="end" name="end" value="{{ request()->get('end')??date('Y-m-d')  }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('party') ? 'has-error' :'' }}">
                                    <label for="party">Party <span class="text-danger">*</span></label>
                                    <select required class="form-control select2" id="party" style="width: 100%;"
                                            name="party" data-placeholder="Search Party">
                                        <option value="">Search Party</option>
                                        @if (request('party') != '')
                                            <option value="{{ request('party') }}" selected>{{ request('party_select_name') }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="party_select_name" id="party_select_name"
                                           value="{{ request('party_select_name') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="submit" name="search" class="btn btn-primary bg-gradient-success form-control"
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
        @if(count($aits) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <a href="#" onclick="getprint('printArea')" class="btn btn-success btn-sm">Print</a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive-sm" id="printArea">
                                <div class="row print-heading" >
                                    <div class="col-12">
                                        <h1 class="text-center m-0" style="font-size: 40px !important;font-weight: bold">
{{--                                            <img height="50px" src="{{ asset('themes/backend/files/assets/images/logo.png') }}" alt="">--}}
                                            {{ config('app.name') }}
                                        </h1>
                                        <h3 class="text-center m-0" style="font-size: 25px !important;">Party wise AIT list</h3>
                                        <h3 class="text-center m-0" style="font-size: 20px !important;">Party: {{ request('party_select_name') }}</h3>
                                        <h3 class="text-center m-0 mb-2" style="font-size: 19px !important;">Date: {{ count($aits) > 0 ? \Carbon\Carbon::parse($aits[0]->date)->format('d-m-Y') : '' }} to {{ count($aits) > 0 ? \Carbon\Carbon::parse($aits[count($aits) - 1]->date)->format('d-m-Y') : '' }}</h3>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="table" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Base Amount</th>
                                            <th class="text-center">Rate</th>
                                            <th class="text-center">Amount of AIT</th>
                                            <th class="extra_column"></th>
                                        </tr>
                                        </thead>
                                        <?php
                                        $totalBaseAmount = 0;
                                        ?>
                                        <tbody>
                                        @foreach($aits as $ait)
                                            <tr>

                                                <td class="text-center">{{ \Carbon\Carbon::parse($ait->date)->format('d-m-Y') }}</td>

                                                <td class="text-right">{{ number_format($ait->base_amount ?? 0,2) }}</td>
                                                <td class="text-right">{{ number_format(5 ?? 0,2) }}</td>
                                                <td class="text-right">{{ number_format($ait->amount,2) }}</td>
                                                <td class="extra_column">
                                                    @if(\File::exists(public_path($ait->ait_document)))
                                                        <a href="{{ asset($ait->ait_document) }}" download class="btn btn-success btn-sm extra_column">Pdf</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php
                                            $totalBaseAmount += $ait->base_amount ?? 0;
                                            ?>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="" class="text-right">Total Amount</th>
                                            <th class="text-right" colspan="">{{ number_format($totalBaseAmount,2) }}</th>
                                            <th colspan="" class="text-right"></th>
                                            <th class="text-right" colspan="">{{ number_format($aits->sum('amount'),2) }}</th>
                                            <th class="extra_column"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
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
            $('#party').select2({
                ajax: {
                    url: "{{ route('get_suppliers_json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
            $('#party').on('select2:select', function (e) {
                var data = e.params.data;
                var index = $("#party").index(this);
                $('#party_select_name:eq(' + index + ')').val(data.text);
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
