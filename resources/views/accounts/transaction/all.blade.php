@extends('layouts.app')
@section('title','Transaction')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <a href="{{ route('transaction.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Transaction Create <i class="fa fa-plus"></i></a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Account Head Type</th>
                                <th>Account Head Sub Type</th>
                                <th>Amount</th>
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
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Information</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="type" id="type">

                    <div class="form-group">
                        <label>Amount</label>
                        <input class="form-control" name="amount" placeholder="Enter Amount" id="amount">
                    </div>

                    <div class="form-group">
                        <label>Note</label>
                        <input class="form-control" name="note" placeholder="Enter Note" id="note">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="modal-btn-edit">Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection
@section('script')
    <script>
        $(function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('transaction.datatable') }}',
                columns: [
                    {data: 'date', name: 'date'},
                    {data: 'transaction_type', name: 'transaction_type'},
                    {data: 'accountHeadType', name: 'accountHeadType.name'},
                    {data: 'accountHeadSubType', name: 'accountHeadSubType.name'},
                    {data: 'amount', name: 'amount'},
                    {data: 'action', name: 'action', orderable: false},
                ],
                order: [[ 0, "desc" ]],
            });

            $('body').on('click', '.btn-edit', function () {
                var transactionId = $(this).data('id');

                $.ajax({
                    method: "GET",
                    url: "{{ route('transaction.details_json') }}",
                    data: { transactionId: transactionId }
                }).done(function( response ) {
                    $('#id').val(response.id);
                    $('#type').val(response.transaction_type);
                    $('#amount').val(response.amount);
                    $('#note').val(response.note);

                    $('#modal-edit').modal('show');
                });
            });

            $('#modal-btn-edit').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('transaction.edit_post') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $('#modal-edit').modal('hide');
                            Swal.fire(
                                'Update!',
                                response.message,
                                'success'
                            ).then((result) => {
                                location.reload();
                                //window.location.href = response.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        })
    </script>
@endsection
