@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('title')
    Transaction Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.transaction') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Start Date</label>

                                    <input type="text" class="form-control pull-right"
                                               id="start" name="start" value="{{ request()->get('start')  }}" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>End Date</label>

                                    <input type="text" class="form-control pull-right"
                                               id="end" name="end" value="{{ request()->get('end')  }}" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control select2" name="type" id="account_head_type">
                                        <option value="">All Type</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ request()->get('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sub Type</label>
                                    <select class="form-control select2" name="sub_type" id="account_head_sub_type">
                                        <option value="">All Sub Type</option>
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

    <div class="row">
        <div class="col-sm-12" style="min-height:300px">
            @if($result)
                <section class="card">

                    <div class="card-body">
                        <button class="pull-right btn btn-primary" onclick="getprint('prinarea')">Print</button><br><hr>

                        <div class="adv-table" id="prinarea">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Account Head</th>
                                    <th>Account Sub Head</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($result as $item)
                                    <tr>
                                        <td>{{ $item->department->name ?? '' }}</td>
                                        <td>{{ $item->accountHead->name }}</td>
                                        <td>{{ $item->accountSubHead->name }}</td>
                                        <td class="text-right">{{ $item->amount }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr>
                                    <th class="text-right" colspan="3">Total</th>
                                    <th class="text-right">{{ $result->sum('amount') }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            var accountHeadSubTypeSelected = '{{ request()->get('sub_type') }}';

            //Date picker
            $('#start, #end').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            //Initialize Select2 Elements
            $('.select2').select2();

            $('#account_head_type').change(function () {
                var typeId = $(this).val();

                $('#account_head_sub_type').html('<option value="">All Sub Type</option>');

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

            $('#account_head_type').trigger('change');
        });

        var APP_URL = '{!! url()->full()  !!}';
        function getprint(print) {

            $('body').html($('#'+print).html());
            window.print();
            window.location.replace(APP_URL)
        }
    </script>
@endsection
