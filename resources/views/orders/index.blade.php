@extends('layouts.app')
@section('title', 'Department')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <strong>All Orders</strong>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Shipping Address</th>
                                    <th>Shipping City</th>
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
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('order.datatable') }}',
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
                        data: 'pi_first_name',
                        name: 'pi_first_name'
                    },
                    {
                        data: 'pi_email',
                        name: 'pi_email'
                    },
                    {
                        data: 'pi_phone_no',
                        name: 'pi_phone_no'
                    },
                    {
                        data: 'shipping_address',
                        name: 'shipping_address'
                    },
                    {
                        data: 'shipping_city',
                        name: 'shipping_city'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data === 'pending') {
                                return '<span class="badge badge-warning">Pending</span>';
                            } else if (data === 'processing') {
                                return '<span class="badge badge-info">Processing</span>';
                            } else if (data === 'completed') {
                                return '<span class="badge badge-success">Completed</span>';
                            } else if (data === 'cancelled') {
                                return '<span class="badge badge-danger">Cancelled</span>';
                            } else {
                                return '<span class="badge badge-secondary">Unknown</span>';
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
        });
    </script>
@endsection
