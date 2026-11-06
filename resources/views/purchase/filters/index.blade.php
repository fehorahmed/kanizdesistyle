@extends('layouts.app')
@section('title','Filters')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <a href="{{ route('filters.create') }}" class="btn btn-primary bg-gradient-primary btn-sm">
                    Add Filter <i class="fa fa-plus"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="filters-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S/L</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Options</th>
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
    $('#filters-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('filters.datatable') }}',
        "pagingType": "full_numbers",
        "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]],
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'type', name: 'type'},
            {data: 'options', name: 'options'},
            {data: 'action', name: 'action', orderable: false},
        ],
        "dom": 'lBfrtip',
        "buttons": datatableButtons(),
        "responsive": true, 
        "autoWidth": false,
        "colReorder": true,
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
                    url: "{{ route('filters.destroy', ['filter' => 'REPLACE_WITH_ID_HERE']) }}".replace('REPLACE_WITH_ID_HERE', id),
                    data: { id: id }
                }).done(function(response) {
                    preloaderToggle(false);
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        ).then(() => location.reload());
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
