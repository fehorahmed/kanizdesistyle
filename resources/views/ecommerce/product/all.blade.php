@extends('layouts.app')
@section('title', 'Product')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <a href="{{ route('ecom_product.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Product
                        Create <i class="fa fa-plus"></i></a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Selling Price</th>
                                    <th>Discount(%)</th>
                                    <th>Alert</th>
                                    <th>Image</th>
                                    <th>Is Show</th>
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
                        duration: 300, // don't foget to change the duration also in CSS
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
                    url: '{{ route('ecom_product.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        const urlParams = new URLSearchParams(window.location.search);
                        d.offer = urlParams.get('offer'); // gets 'offer' from URL
                    }
                },
                // ajax: '{{ route('ecom_product.datatable') }}',
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
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'brand',
                        name: 'brand'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'old_selling_price',
                        name: 'old_selling_price'
                    },
                    {
                        data: 'discount_percent',
                        name: 'discount_percent'
                    },
                    {
                        data: 'alert_quantity',
                        name: 'alert_quantity'
                    },
                    {data: 'image', name: 'image'},
                    {
                        data: 'is_show',
                        name: 'is_show',
                    },
                    {
                        data: 'status',
                        name: 'status',
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
