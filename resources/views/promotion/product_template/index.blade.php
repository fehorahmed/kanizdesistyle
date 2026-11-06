@extends('layouts.app')
@section('title','Product Templates')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <a href="{{ route('product-template.create') }}" class="btn btn-primary bg-gradient-primary btn-sm">Product Template Create <i class="fa fa-plus"></i></a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Title</th>
                                <th>Products</th>
                                <th>End Date</th>
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
                ajax: '{{ route('product-template.datatable') }}',
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'main_heading', name: 'main_heading'},
                    {data: 'products', name: 'products'},
                    {data: 'end_date', name: 'end_date'},
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            if (data === 1) {
                                return '<span class="badge badge-success">Active</span>';
                            } else if (data === 0) {
                                return '<span class="badge badge-danger">Inactive</span>';
                            }
                            return data;
                        }
                    },
                    {data: 'action', name: 'action', orderable: false},
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": true, "autoWidth": false,"colReorder": true,
            });
            $('body').on('click', '.btn-delete', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        preloaderToggle(true);
                        $.ajax({
                            method: "DELETE",
                            url: "{{ route('brand.destroy', ['brand' => 'REPLACE_WITH_ID_HERE']) }}".replace('REPLACE_WITH_ID_HERE', id),
                            data: { id: id }
                        }).done(function( response ) {
                            preloaderToggle(false);
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message,
                                });
                            }
                        });

                    }
                })

            });
        });
    </script>
@endsection
