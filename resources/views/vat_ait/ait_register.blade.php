@extends('layouts.app')
@section('title','AIT Register')
@section('style')
    <style>
        .custom-check-box{
            height: calc(1.25rem + 2px);
        }
        .table-bordered thead td, .table-bordered thead th {
            vertical-align: middle;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #000;
            vertical-align: middle;
            border-bottom-width: 2px;
            text-align: center;
        }
        .table thead th {
            vertical-align: middle;
            border-bottom: 2px solid #000000;
        }
        @media print{
            @page {
                size: auto;
                margin: 20px !important;
            }
        }
        .big-checkbox{
            width: 25px;
            height: 25px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('report.ait_register') }}" method="get">
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
                                <div class="row print-heading" style="display: none">
                                    <div class="col-12">
                                        <h1 class="text-center m-0" style="font-size: 40px !important;font-weight: bold">
                                            <img height="50px" src="{{ asset('img/logo.png') }}" alt="">
                                            {{ config('app.name') }}
                                        </h1>
                                        <h3 class="text-center m-0" style="font-size: 25px !important;">AIT Register</h3>
                                        <h3 class="text-center m-0 mb-2" style="font-size: 19px !important;">Date: {{ request('start') }} to {{ request('end') }}</h3>
                                    </div>
                                </div>
                                <form id="ait_payout_form" name="ait_payout_form"  method="post">
                                    @csrf
                                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                                    <div class="table-responsive">
                                        <table id="table" class="table table-bordered">
                                            <thead>
                                            <tr class="extra_column">
                                                <td class="extra_column" style="width: 60px !important">
                                                    Check All <br>
                                                    <input type="checkbox" id="checkAll" name="check_out" value="" class="ait_id big-checkbox">
                                                </td>
                                                <th class="text-center extra_column" style="width: 120px !important">
                                                    <button id="ait_payout_form_save" class="btn btn-sm btn-primary">Payout Save</button>
                                                </th>
                                                <th class="text-center" colspan=11"></th>
                                            </tr>
                                            <tr>
                                                <th class="text-center extra_column">Action</th>
                                                <th class="text-center">Date</th>
                                                <th class="text-center">Party</th>
                                                <th class="text-center">Base Amount</th>
                                                <th class="text-center">Amount of AIT</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $totalAit = 0;
                                            ?>
                                            @foreach($aits as $ait)
                                                <tr>
                                                    <td class="extra_column">
                                                        <input type="checkbox" name="ait_id[]" value="{{ $ait->id }}" class="ait_id big-checkbox">
                                                    </td>
                                                    <td class="text-center">
                                                        {{$ait->date}}
                                                    </td>
                                                    <td>{{ $ait->supplier->name ?? '' }}</td>
                                                    <td class="text-center">
                                                        {{ number_format($ait->invoice->base_amount,2) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($ait->amount,2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th class="extra_column"></th>
                                                <th colspan="3" class="text-right">Total Amount</th>
                                                <th class="text-right" colspan="">{{ number_format($aits->sum('amount'),2) }}</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </form>
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

    <div class="modal fade" id="document-upload-modal" data-backdrop="static" data-keyboard="false"style="z-index: 1050 !important">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Document</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="document">Payment Date <span class="text-danger">*</span></label>
                                    <select name="bank_id" id="bank_id" class="form-control select2">
                                        <option value="">Select bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->bank_name }} (Ac : {{ $bank->ac_number }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label  for="document">Upload Document(pdf) <span class="text-danger">*</span></label>
                                    <input type="file" id="document" name="document" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label for="document">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" id="date" name="payment_date" class="form-control" >
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="modal-btn-save" class="btn btn-primary">Confirm</button>
                </div>
            </div>

        </div>

    </div>

@endsection

@section('script')
    <script>
        $(function () {
            $('#start_date, #end_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });

            $('body').on('click', '#modal-btn-save', function () {
                var formData = new FormData($('#ait_payout_form')[0]);
                formData.append('bank_id', $("#bank_id").val());
                formData.append('document', $('#document')[0].files[0]);
                formData.append('payment_date', $('#date').val());
                $.ajax({
                    type: "POST",
                    url: "{{ route('report.ait_payout') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $("#document-upload-modal").modal('hide');
                            Swal.fire(
                                'Payout & payment voucher created!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                                //window.location.href = response.redirect_url;
                            });
                        } else {
                            // $("#document-upload-modal").modal('hide');
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });

            });


            $('body').on('click', '#ait_payout_form_save', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure to payout?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Save It!'

                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#document-upload-modal").modal('show');
                    }
                })

            });

            $("#checkAll").click(function () {
                $('input:checkbox').not(this).prop('disabled', this.disabled);
                $('input:checkbox').not(this).prop('checked', this.checked);
                allCheckRegister();
            });
        })

        function allCheckRegister(){
            $('.ait_id').attr("disabled", false);
        }
        var APP_URL = '{!! url()->full()  !!}';

        function getprint(print) {
            $('.print-heading').css('display','block');
            $('.extra_column').remove();
            $('body').html($('#' + print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
