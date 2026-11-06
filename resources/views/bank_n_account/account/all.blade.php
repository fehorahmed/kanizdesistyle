@extends('layouts.app')
@section('title','Bank Account')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <a href="{{ route('bank_account.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Bank Account Create <i class="fa fa-plus"></i></a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Account Name</th>
                                <th>Account No.</th>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Opening Balance</th>
                                <th>Balance</th>
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
                ajax: '{{ route('bank_account.datatable') }}',

                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'account_name', name: 'account_name'},
                    {data: 'account_no', name: 'account_no'},
                    {data: 'bank_name', name: 'bank_name'},
                    {data: 'branch_name', name: 'branch_name'},
                    {data: 'account_code', name: 'account_code'},
                    {data: 'description', name: 'description'},
                    {data: 'opening_balance', name: 'opening_balance'},
                    {data: 'balance', name: 'balance'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "columnDefs": [
                    {"className": "text-left", "targets": "5"}
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": true, "autoWidth": false,"colReorder": true,
            });
        })
    </script>
@endsection
