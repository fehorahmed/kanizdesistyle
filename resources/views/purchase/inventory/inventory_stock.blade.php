@extends('layouts.app')
@section('title','Purchase Inventory')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Code</th>
                                <th>Department</th>
                                <th>Quantity</th>
                                {{-- <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Total Cost Price</th>
                                <th>Total Selling Price</th> --}}
                                {{-- <th>Action</th> --}}
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
        $(document).on('click', '.image_preview', function (e) {
            e.preventDefault();
            $(this).magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                closeBtnInside: false,
                fixedContentPos: true,
                mainClass: 'mfp-no-margins mfp-with-zoom',
                image: {
                    verticalFit: true
                },
                zoom: {
                    enabled: true,
                    duration: 300
                }
            }).magnificPopup('open');
        });

        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('inventory_stock.datatable') }}",
            },
            pagingType: "full_numbers",
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'product_name', name: 'products.name' },
                { data: 'product_image', name: 'product_image' , searchable: false},
                { data: 'product_code', name: 'products.code' },
                { data: 'department_name', name: 'departments.name' },
                { data: 'total_quantity', name: 'total_quantity' },
                // { data: 'unit_price', name: 'unit_price' },
                // { data: 'selling_price', name: 'selling_price' },
                // { data: 'total_unit_price', name: 'total_unit_price' },
                // { data: 'total_selling_price', name: 'total_selling_price' },
                // { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            columnDefs: [
                { className: "text-left", targets: [5] }
            ],
            dom: 'lBfrtip',
            buttons: datatableButtons(),
            responsive: true,
            autoWidth: false,
            colReorder: true,
        });
    });
</script>
@endsection
