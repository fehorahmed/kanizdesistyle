@extends('layouts.app')
@section('title','VAT Register')
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
                    <form action="{{ route('vat_payment') }}" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start">Start Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control date-picker" id="start_date" name="start_date" value="{{ request()->get('start_date')??date('d-m-Y')  }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end">End Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control date-picker" id="end_date" name="end_date" value="{{ request()->get('end_date')??date('d-m-Y')  }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="submit" name="search" class="btn btn-primary bg-gradient-primary form-control" value="Search">
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
        @if(count($saleVats) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <a href="#" onclick="getprint('printArea')" class="btn btn-primary btn-sm">Print</a>
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
                                        <h3 class="text-center m-0" style="font-size: 25px !important;">VAT Register</h3>
                                        <h3 class="text-center m-0 mb-2" style="font-size: 19px !important;">Date: {{ request('start') }} to {{ request('end') }}</h3>
                                    </div>
                                </div>
                                <form id="vat_payout_form" name="vat_payout_form"  method="post">
                                    @csrf
                                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                                    <div class="table-responsive">
                                        <table id="table" class="table table-bordered">
                                            <thead>
                                                <tr class="extra_column">
                                                    <td class="extra_column" style="width: 60px !important">
                                                        Check All <br>
                                                        <input type="checkbox" id="checkAll" name="check_out" value="" class="vat_id big-checkbox">
                                                    </td>
                                                    <th class="text-center extra_column" style="width: 120px !important">
                                                        <button id="vat_payout_form_save" class="btn btn-sm btn-primary">Payout Save</button>
                                                    </th>
                                                    <th class="text-center" colspan=11"></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center extra_column">Action</th>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Party</th>
                                                    <th class="text-center">Base Amount</th>
                                                    <th class="text-center">Amount of Vat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $totalVat = 0;
                                                ?>
                                                @foreach($saleVats as $vat)
                                                    <tr>
                                                        <td class="extra_column">
                                                            <input type="checkbox" name="vat_id[]" value="{{ $vat->id }}" class="vat_id big-checkbox">
                                                        </td>
                                                        <td class="text-center">
                                                            {{$vat->date}}
                                                        </td>
                                                        <td>{{ $vat->customer->name ?? '' }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($vat->invoice->sub_total,2) }}
                                                        </td>
                                                        <td class="text-right">
                                                            {{ number_format($vat->amount,2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="extra_column"></th>
                                                    <th colspan="3" class="text-right">Total Amount</th>
                                                    <th class="text-right" colspan="">{{ number_format($saleVats->sum('amount'),2) }}</th>
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
                    <h4 class="modal-title">Payment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="vat-payment-form" enctype="multipart/form-data" name="modal-form">
                        <div class="row">
                            <div class="col-md-12">
                                <div
                                    class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                                    <label for="date">Date <span
                                            class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off"
                                        value="{{ old('date', date('d-m-Y')) }}" name="date"
                                        id="date" class="form-control date-picker"
                                        placeholder="Enter date">
                                    @error('date') 
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <select class="form-control select2" id="modal-pay-type"
                                        name="payment_type">
                                        <option value="">Select Option</option>
                                        <option value="1"
                                            {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="2"
                                            {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank
                                        </option>
                                        <option value="3"
                                            {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking
                                        </option>
                                    </select>
                                </div>
                                <div id="mobile-bank-info">
                                    <div class="form-group">
                                        <label class="">Mobile Banking Account</label>
                                        <select class="form-control select2" id="mobile_banking" name="mobile_banking">
                                            <option value="">Select Account</option>
                                            @foreach ($mobileBankings as $mobileBanking)
                                                <option value="{{ $mobileBanking->id }}">
                                                    {{ $mobileBanking->account_name }}-{{ $mobileBanking->account_no }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="modal-bank-info">
                                    <div
                                        class="form-group {{ $errors->has('account') ? 'has-error' : '' }}">
                                        <label> Bank Account</label>
                                        <select class="form-control select2" id="modal-account"
                                            name="account">
                                            <option value="">Select Account</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->account_no }} -
                                                    {{ $account->branch->name ?? '' }} -
                                                    {{ $account->bank->name ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="cheque_payment">
                                        <div class="form-group">
                                            <label>Cheque No.</label>
                                            <input class="form-control" type="text"
                                                name="cheque_no" placeholder="Enter Cheque No."
                                                value="{{ old('cheque_no') }}">
                                        </div>
                                        <div
                                            class="form-group {{ $errors->has('cheque_image') ? 'has-error' : '' }}">
                                            <label>Cheque Image</label>
                                            <input class="form-control" name="cheque_image"
                                                type="file">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  for="document">Upload Document(pdf) <span class="text-danger">*</span></label>
                                    <input type="file" id="document" name="document" class="form-control" >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div
                                    class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                                    <label for="notes">Notes</label>
                                    <input type="text" value="{{ old('notes') }}"
                                        name="notes" id="notes" class="form-control"
                                        placeholder="Enter Notes">
                                    @error('notes')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
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

            $('#modal-pay-type').change(function() {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#modal-bank-info').hide();
                    $('#mobile-bank-info').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info').show();
                    }
                } else if($(this).val() == '2') {
                    $('#modal-bank-info').show();
                    $('#mobile-bank-info').hide();
                }else {
                    $('#modal-bank-info').hide();
                    $('#mobile-bank-info').hide();
                }
            });
            $('#modal-pay-type').trigger('change');

            $('body').on('click', '#modal-btn-save', function () {
                var formData = new FormData($('#vat-payment-form')[0]);
                var form2Data = new FormData($('#vat_payout_form')[0]);
                let vatIds = form2Data.getAll('vat_id[]');
                // Append each value manually
                vatIds.forEach(id => {
                    formData.append('vat_id[]', id);
                });
                formData.append('start_date', form2Data.get('start_date'));
                formData.append('end_date', form2Data.get('end_date'));

                $.ajax({
                    type: "POST",
                    url: "{{ route('vat_payout') }}",
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


            $('body').on('click', '#vat_payout_form_save', function (e) {
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
                        // $('#vat_payout_form').submit();
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
            $('.vat_id').attr("disabled", false);
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
