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
                                <th>Batch No</th>
                                <th>Department</th>
                                <th>Pack Size</th>
                                <th>Pack Qty</th>
                                <th>Cost Price/U</th>
                                <th>Selling Price/U</th>
                                <th>Total Cost Price</th>
                                <th>Total Selling Price</th>
                                <th>Profit Margin</th>
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
            $(document).on('click', '.zoom-gallery', function (e) {
                e.preventDefault();
                $(this).magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    closeOnContentClick: false,
                    closeBtnInside: false,
                    mainClass: 'mfp-with-zoom mfp-img-mobile',
                    allowHTMLInTemplate: true,
                    image: {
                        verticalFit: true,
                        titleSrc: function(item) {
                            return item.el.attr('title') + ' &middot; <a class="image-source-link" href="'+item.el.attr('data-source')+'" target="_blank">image source</a>';
                        }
                    },
                    gallery: {
                        enabled: true
                    },
                    zoom: {
                        enabled: true,
                        duration: 300,
                        opener: function(element) {
                            return element.find('img');
                        }
                    }
                }).magnificPopup('open');

            });

            $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('out_of_stock.datatable') }}",
                },

                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'product', name: 'product.name'},
                    {data: 'product_image', name: 'product_image',searchable: false},
                    {data: 'product_code', name: 'product.code'},
                    {data: 'batch_no', name: 'batch_no'},
                    {data: 'department', name: 'department.name'},
                    {data: 'pack_size', name: 'pack_size'},
                    {data: 'pack_quantity', name: 'pack_quantity'},
                    {data: 'unit_price', name: 'unit_price'},
                    {data: 'selling_price', name: 'selling_price'},
                    {data: 'total_unit_price', name: 'total_unit_price'},
                    {data: 'total_selling_price', name: 'total_selling_price'},
                    {data: 'profit_margin', name: 'profit_margin'},
                    {data: 'action', name: 'action'},
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



{{--


@extends('layouts.app')
@section('title','Purchase Inventory')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <table id="table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th> Code </th>
                            <th>Department</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
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
                ajax: '{{ route('purchase_inventory.datatable') }}',
                columns: [
                    {data: 'product', name: 'product.name'},
                    {data: 'product_code', name: 'product.code'},
                    {data: 'department', name: 'department.name'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'total_unit_price', name: 'total_unit_price'},
                    {data: 'action', name: 'action'},
                ],
            });
        });
    </script>
@endsection --}}
