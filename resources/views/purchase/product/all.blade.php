@extends('layouts.app')
@section('title', 'Product')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <a href="{{ route('product.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Product
                        Create <i class="fa fa-plus"></i></a>
                </div>
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control select2" id="category" name="category">
                                    <option value="">Select Option</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sub Category</label>
                                <select class="form-control select2" id="sub_category" name="sub_category">
                                    <option value="">Select Option</option>
                                    {{-- @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brand</label>
                                <select class="form-control select2" id="brand" name="brand">
                                    <option value="">Select Option</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
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
                                    <th>Code</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Discount(%)</th>
                                    {{-- <th>Vat(%)</th> --}}
                                    <th>Alert</th>
                                    <th>Image</th>
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
                        
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('product.datatable') }}",
                    data: function(d) {
                        d.category = $('#category').val();
                        d.sub_category = $('#sub_category').val();
                        d.brand = $('#brand').val();
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
                        data: 'discount_percent',
                        name: 'discount_percent'
                    },
                    // {
                    //     data: 'vat_percent',
                    //     name: 'vat_percent'
                    // },
                    {
                        data: 'alert_quantity',
                        name: 'alert_quantity'
                    },
                    {data: 'image', name: 'image'},
                    {data: 'status',name: 'status'},
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

            $('#brand').change(function(){
                table.ajax.reload();
            });
            $('#sub_category').change(function(){
                table.ajax.reload();
            });
            $('#category').change(function(){
                table.ajax.reload();
                let category = $(this).val();
                $('#sub_category').html('<option value="">Select Option</option>');
                if(category != ''){
                    $.ajax({
                        method: "GET",
                        url: '{{ route('get_product_sub_category') }}',
                        data: {category: category}
                    }).done(function(response){
                        if(response.status){
                            $.each(response.sub_categories,function(i,item){
                                $('#sub_category').append('<option value="'+ item.id +'">'+ item.name +'</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
