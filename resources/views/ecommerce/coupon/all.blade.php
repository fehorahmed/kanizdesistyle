@extends('layouts.app')
@section('title','Coupons')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-default">
                <div class="card-header">
                    <a href="{{ route('ecom_coupon.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">
                        Create Coupon <i class="fa fa-plus"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="couponTable" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Type</th>
                                <th>Min Purchase</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
$(function () {
    var table = $('#couponTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('ecom_coupon.datatable') }}',
        pagingType: "full_numbers",
        dom: 'T<"clear">lfrtip',
        lengthMenu: [[10, 25, 50, -1],[10, 25, 50, "All"]],
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'code', name: 'code'},
            {data: 'discount', name: 'discount'},
            {data: 'type', name: 'type'},
            {data: 'min_purchase', name: 'min_purchase'},
            {data: 'is_active', name: 'is_active'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        drawCallback: function() {
            // Initialize Bootstrap Switches
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
        },
        columnDefs: [
            {"className": "text-left", "targets": [5]}
        ],
        responsive: false,
        autoWidth: false,
    });

    // Delete Coupon
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
                    url: "{{ route('ecom_coupon.delete', ['coupon' => 'REPLACE_WITH_ID_HERE']) }}".replace('REPLACE_WITH_ID_HERE', id),
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

    // Toggle Status
    $('#couponTable').on('switchChange.bootstrapSwitch', '.coupon-status-switch', function (event, state) {
        var id = $(this).data('id');
        var newStatus = state ? 1 : 0;

        $.ajax({
            method: "POST",
            url: "{{ route('ecom_coupon.toggleStatus', ['coupon' => 'REPLACE_ID']) }}".replace('REPLACE_ID', id),
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            }
        }).done(function(response) {
            if(!response.success){
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Something went wrong!', 'error');
        });
    });
});
</script>
@endsection
