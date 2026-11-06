@extends('layouts.app')
@section('title','Purchase Receipt')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                {{-- <form enctype="multipart/form-data" action="{{ route('purchase_import') }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class=" row {{ $errors->has('excel_file') ? 'has-error' :'' }}">
                            <label for="excel_file"  class="col-sm-2">Purchase Excel File <span class="text-danger">*</span></label>
                            <div class="col-sm-7">
                                <input type="file" style="padding: .2rem .75rem !important;width: 50% !important;" name="excel_file" class="form-control" id="excel_file">
                                @error('excel_file')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-3">
                                <a class="btn btn-danger" href="{{ asset('excel/purchase-order.xlsx') }}">
                                        <i class="fas fa-cloud-download-alt"></i>
                                        Download Demo Excel
                                    </a>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
                <hr>                <!-- /.card-header --> --}}
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Order No</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right">Page Total:</th>
                                    <th id="total_sum"></th>
                                    <th id="paid_sum"></th>
                                    <th id="due_sum"></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
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
                ajax: {
                    url: "{{ route('purchase_receipt.datatable') }}",
                },
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'date', name: 'date'},
                    {data: 'order_no', name: 'order_no'},
                    {data: 'supplier', name: 'supplier.name'},
                    {data: 'total', name: 'total'},
                    {data: 'paid', name: 'paid'},
                    {data: 'due', name: 'due'},
                    {data: 'user', name: 'user.name'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                "columnDefs": [
                    {"className": "text-left", "targets": "5"}
                ],
                "dom": 'lBfrtip',
                "buttons": datatableButtons(),
                "responsive": true, "autoWidth": false,"colReorder": true,
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // function for parsing number
                    var parseValue = function (i) {
                        if (typeof i === 'string') {
                            var numeric = i.replace(/<[^>]*>/g, ''); // remove HTML
                            numeric = numeric.replace(/[^0-9.-]+/g, ''); // remove currency/comma
                            return parseFloat(numeric) || 0;
                        }
                        return typeof i === 'number' ? i : 0;
                    };

                    // Total column sum (index 4)
                    var totalSum = api
                        .column(4, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return parseValue(a) + parseValue(b);
                        }, 0);

                    // Paid column sum (index 5)
                    var paidSum = api
                        .column(5, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return parseValue(a) + parseValue(b);
                        }, 0);

                    // Due column sum (index 6)
                    var dueSum = api
                        .column(6, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return parseValue(a) + parseValue(b);
                        }, 0);

                    $('#total_sum').html(totalSum.toFixed(2));
                    $('#paid_sum').html(paidSum.toFixed(2));
                    $('#due_sum').html(dueSum.toFixed(2));
                }
            });

            $('body').on('click', '.btn-delete', function() {
                var orderId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the order This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('purchase_delete') }}",
                            type: "POST",
                            data: {
                                orderId: orderId
                            },
                            success: function(response) {
                                console.log(response);
                                if(response.success){
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                }else{
                                    Swal.fire(
                                        'Error!',
                                        response.error || 'An error occurred while deleting the order.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                     response.error || 'An error occurred while deleting the order.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

        })
    </script>
@endsection
