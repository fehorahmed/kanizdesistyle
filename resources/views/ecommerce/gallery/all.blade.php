@extends('layouts.app')
@section('title','Gallary')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <!-- /.card-header -->
                <div class="card-header">
                    <a href="{{ route('ecom_gallery.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Gallery
                        Create <i class="fa fa-plus"></i></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Image</th>
                                <th>Video</th>
                                <th>Sort</th>
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
                ajax: '{{ route('ecom_gallery.datatable') }}',

                "pagingType": "full_numbers",
                "dom": 'T<"clear">lfrtip',
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'image', name: 'image', searchable: true},
                    {data: 'video_url', name: 'video_url', searchable: true},
                    {data: 'sort', name: 'sort'},
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
