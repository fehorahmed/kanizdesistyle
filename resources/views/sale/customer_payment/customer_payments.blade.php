@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Customer Payment
@endsection

@section('content')
    {{-- @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('message') }}
        </div>
    @endif --}}

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th> S/N </th>
                            <th> Date </th>
                            <th> Customer </th>
                            {{-- <th> Department </th> --}}
                            <th> User  </th>
                            <th> Amount  </th>
                            <th> Action </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        var due;

        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("customer_payments", ["customer"=>$customer->id]) }}',
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'date', name: 'date'},
                    {data: 'customer', name: 'customer'},
                    // {data: 'sister_concern', name: 'sister_concern', orderable: false},
                    {data: 'user', name: 'user', orderable: false},
                    {data: 'amount', name: 'amount', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": true, "autoWidth": false,"colReorder": true,
            });

            //Date picker
            $('#date, #next-payment-date, #date-refund').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });

    </script>
@endsection
