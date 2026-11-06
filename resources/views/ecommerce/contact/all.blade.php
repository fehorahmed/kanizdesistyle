@extends('layouts.app')
@section('title','Contact')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <!-- /.card-header -->
                {{-- <div class="card-header">
                    <a href="{{ route('ecom_contact.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Contact
                        Create <i class="fa fa-plus"></i></a>
                </div> --}}
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Subject</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile No</th>
                                <th>Message</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true, 
                ajax: '{{ route('ecom_contact.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'subject', name: 'subject', searchable: false},
                    {data: 'name', name: 'name', searchable: true},
                    {data: 'email', name: 'email'},
                    {data: 'mobile_no', name: 'mobile_no'},
                    {data: 'message', name: 'message'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "columnDefs": [
                    {"className": "text-left", "targets": "5"}
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": false, "autoWidth": false,
            });

        })
    </script>
@endsection
