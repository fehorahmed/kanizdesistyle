@extends('layouts.app')

@section('title','Transaction Add')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Transaction Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('transaction.add') }}">
                    @csrf

                    <div class="card-body">

                        <div class="form-group row {{ $errors->has('type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="type" id="type">
                                    <option value="">Select Type</option>
                                    <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>Receive</option>
                                    <option value="2" {{ old('type') == '2' ? 'selected' : '' }}>Payment</option>
                                </select>

                                @error('type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('account_head_type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Account Head Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="account_head_type" id="account_head_type">
                                    <option value="">Select Account Head Type</option>
                                </select>

                                @error('account_head_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('account_head_sub_type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Account Head Sub Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="account_head_sub_type" id="account_head_sub_type">
                                    <option value="">Select Account Head Sub Type</option>
                                </select>

                                @error('account_head_sub_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('payment_type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Payment Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="payment_type" id="payment_type">
                                    <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                                    <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank</option>
                                    <option value="3" {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                </select>

                                @error('payment_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div id="mobile-bank-info">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Mobile Banking Account</label>
                                <div class="col-md-10">
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
                        </div>
                        <div id="bank-info">
                            <div class="form-group row {{ $errors->has('bank') ? 'has-error' :'' }}">
                                <label class="col-sm-2 col-form-label">Bank *</label>

                                <div class="col-sm-10">
                                    <select class="form-control select2" name="bank" id="bank">
                                        <option value="">Select Bank</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ old('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('bank')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('branch') ? 'has-error' :'' }}">
                                <label class="col-sm-2 col-form-label">Branch *</label>

                                <div class="col-sm-10">
                                    <select class="form-control select2" name="branch" id="branch">
                                        <option value="">Select Branch</option>
                                    </select>

                                    @error('branch')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('account') ? 'has-error' :'' }}">
                                <label class="col-sm-2 col-form-label">Account *</label>

                                <div class="col-sm-10">
                                    <select class="form-control select2" name="account" id="account">
                                        <option value="">Select Account</option>
                                    </select>

                                    @error('account')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('cheque_no') ? 'has-error' :'' }}">
                                <label class="col-sm-2 col-form-label">Cheque No</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="cheque_no" placeholder="Enter Cheque No.">

                                    @error('cheque_no')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('cheque_image') ? 'has-error' :'' }}">
                                <label class="col-sm-2 col-form-label">Cheque Image</label>

                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="cheque_image">

                                    @error('cheque_image')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('amount') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Amount *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="amount" placeholder="Enter Amount" value="{{ old('amount') }}">

                                @error('amount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Date *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control date-picker" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('d-m-Y')) : old('date') }}" autocomplete="off">

                                @error('date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('note') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Note</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="note" placeholder="Enter Note" value="{{ old('note') }}">

                                @error('note')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
                        <a href="{{ route('transaction.all') }}" class="btn btn-danger bg-gradient-danger btn-sm float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            var accountHeadTypeSelected = '{{ old('account_head_type') }}';
            var accountHeadSubTypeSelected = '{{ old('account_head_sub_type') }}';
            var branchSelected = '{{ old('branch') }}';
            var accountSelected = '{{ old('account') }}';

            //Initialize Select2 Elements
            $('.select2').select2()
            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('#type').change(function () {
                var type = $(this).val();

                $('#account_head_type').html('<option value="">Select Account Head Type</option>');
                $('#account_head_sub_type').html('<option value="">Select Account Head Sub Type</option>');

                if (type != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_account_head_type') }}",
                        data: { type: type }
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (accountHeadTypeSelected == item.id)
                                $('#account_head_type').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#account_head_type').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('#account_head_type').trigger('change');
                    });
                }
            });

            $('#type').trigger('change');

            $('#account_head_type').change(function () {
                var typeId = $(this).val();

                $('#account_head_sub_type').html('<option value="">Select Account Head Sub Type</option>');

                if (typeId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_account_head_sub_type') }}",
                        data: { typeId: typeId }
                    }).done(function( data ) {
                        $.each(data, function( index, item ) {
                            if (accountHeadSubTypeSelected == item.id)
                                $('#account_head_sub_type').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#account_head_sub_type').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            $('#payment_type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3') {
                    $('#bank-info').hide();
                    $('#mobile-bank-info').hide();
                    if($(this).val() == '3'){
                        $('#mobile-bank-info').show();
                    }
                } else if($(this).val() == '2') {
                    $('#bank-info').show();
                    $('#mobile-bank-info').hide();
                }else {
                    $('#bank-info').hide();
                    $('#mobile-bank-info').hide();
                }
            });

            $('#payment_type').trigger('change');

            $('#bank').change(function () {
                var bankId = $(this).val();
                $('#branch').html('<option value="">Select Branch</option>');
                $('#account').html('<option value="">Select Account</option>');

                if (bankId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_branch') }}",
                        data: { bankId: bankId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (branchSelected == item.id)
                                $('#branch').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#branch').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('#branch').trigger('change');
                    });
                }

                $('#branch').trigger('change');
            });

            $('#bank').trigger('change');

            $('#branch').change(function () {
                var branchId = $(this).val();
                $('#account').html('<option value="">Select Account</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_bank_account') }}",
                        data: { branchId: branchId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (accountSelected == item.id)
                                $('#account').append('<option value="'+item.id+'" selected>'+item.account_no+'</option>');
                            else
                                $('#account').append('<option value="'+item.id+'">'+item.account_no+'</option>');
                        });
                    });
                }
            });
        });
    </script>
@endsection
