@extends('layouts.app')
@section('title', 'Product')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <a href="{{ route('service.create') }}" class="btn btn-primary bg-gradient-primary btn-sm">Service
                        Create <i class="fa fa-plus"></i></a>
                </div>
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Department</label>
                                <select class="form-control select2" id="department">
                                    <option value="">Select Option</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Code</th>
                                    <th>Service Cost Price</th>
                                    <th>Service Sale Price</th>
                                    {{-- <th>Description</th> --}}
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
        $(function() {

            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('service.datatable') }}',
                    data: function(d) {
                        d.department = $('#department').val();
                    }
                },
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'service_cost_price',
                        name: 'service_cost_price'
                    },
                    {
                        data: 'service_sale_price',
                        name: 'service_sale_price'
                    },
                    // {
                    //     data: 'description',
                    //     name: 'description'
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data === 1) {
                                return '<span class="badge badge-success">Active</span>';
                            } else if (data === 0) {
                                return '<span class="badge badge-danger">Inactive</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": true,
                "autoWidth": false,
                "colReorder": true,
            });
            $('#department').change(function(){
                table.ajax.reload();
            });
        });
    </script>
@endsection
