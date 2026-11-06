@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <style>
        .patient_number_suggestion {
            background-color: antiquewhite;
            padding-left: 10px;
            padding-top: 8px;
            padding-bottom: 1px;
        }
    </style>
@endsection

@section('title')
    Sms
@endsection

@section('content')
    <form method="POST" enctype="multipart/form-data" action="{{ route('sms_panel') }}" id="sale_form">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Sms Panel Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('patient') ? 'has-error' :'' }}">
                                    <label>Patient</label>
                                    <select required class="form-control patient select2" style="width: 100%;" name="patient">
                                        <option value="0" {{ old('patient')==0?'selected':'' }}>All Patient</option>
                                        <option value="1" {{ old('patient')==1?'selected':'' }}>Single Patient</option>
                                    </select>
                                    @error('patient')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('message') ? 'has-error' :'' }}">
                                    <label>Message</label>
                                    <textarea class="form-control" name="message">{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Patient Number Search</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <input type="search" class="form-control patient_number_search" id="patient_number_search" name="number_search" value="" placeholder="Enter Patient Name/Number" autofocus autocomplete="off">
                        </div>
                        <div class="row patient_number_suggestion_container">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">Numbers</h3>
                        @error('mobile_numbers')
                            <span class="text-right">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="card-body">
                        <div class="row patient_number_push_container">

                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary submission ">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(function () {
            $('#table').DataTable();
            let phone_numbers = [];

            $('.patient').change(function () {
                let patient = $(this).val();
                if(patient==0){
                    $('.patient_number_search').attr('disabled',true);
                }else {
                    $('.patient_number_search').attr('disabled',false);
                }
            });
            $('.patient').trigger('change');

            $('#patient_number_search').on('input',function () {
                let term = $(this).val();
                $('.patient_number_suggestion_container').html('');
                //alert(branchId);
                if (term != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_patient_number_suggestion') }}",
                        data: { term: term }
                    }).done(function( response ) {
                        $('.patient_number_suggestion_container').html('');
                        if (response.success) {
                            $.each(response.customers, function (index,row) {
                                let html = '<div class="col-md-4 mb-2 suggestion_item">'+
                                    '<div class="patient_number_suggestion" style="cursor: pointer;" data-mobile="'+row.mobile_no+'">'+
                                    '<h6>'+row.name+" - "+row.mobile_no+'</h6>'+
                                    '</div>'+
                                    '</div>';

                                $('.patient_number_suggestion_container').append(html);
                            });
                        }
                    });
                }
            });
            $('body').on('click', '.patient_number_suggestion', function (e) {
                var phone_number = $(this).data('mobile');
                $(this).closest('.suggestion_item').fadeOut();
                if($.inArray(phone_number, phone_numbers) != -1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Already exist in list.',
                    });
                    return false;
                }
                let html = '<div class="col-md-2 mb-1 item_number">'+
                                '<input type="hidden" class="mobile_number" value="'+phone_number+'" name="mobile_numbers[]">'+
                                '<span style="background-color: #0c5460;padding: 2px 7px;border-radius: 7px 0px 0px 7px;color: #fff;">'+ phone_number +'</span><span class="btn-remove" style="background-color: #dc3545;padding: 2px 7px;border-radius: 0px 3px 3px 0px;color: #fff;cursor: pointer;">X</span>'+
                            '</div>';
                $('.patient_number_push_container').append(html);
                phone_numbers.push(phone_number);
            });
            $('body').on('click', '.btn-remove', function () {
                var phone_number = $(this).closest('.item_number').find('.mobile_number').val();
                $(this).closest('.item_number').remove();
                phone_numbers.pop(phone_number);
            });
        })
    </script>
@endsection
