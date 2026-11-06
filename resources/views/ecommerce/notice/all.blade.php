@extends('layouts.app')
@section('title','Notice')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <!-- /.card-header -->
                <div class="card-header">
                    <a href="{{ route('ecom_notice.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Notice
                        Create <i class="fa fa-plus"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Title</th>
                                <th>File</th>
                                <th>Status</th>
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
                ajax: '{{ route('ecom_notice.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'title', name: 'title'},
                    {data: 'pdf_file', name: 'pdf_file', searchable: false},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "columnDefs": [
                    {"className": "text-left", "targets": "5"}
                ],
                "responsive": false, "autoWidth": false,
            });

        })
    </script>
@endsection
