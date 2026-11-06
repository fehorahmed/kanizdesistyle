@extends('layouts.app')
@section('title','Purchase Inventory')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <!-- /.card-header -->
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Stock</label>
                                <select class="form-control select2" id="stock" name="stock">
                                    <option value="">All Product</option>
                                    <option value="1">Stock Product</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table id="table" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Brand</th>
                                <th>Code</th>
                                <th>Batch No</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                @can('cost_price_show')
                                    <th>Cost Price/U</th>
                                @endcan
                                <th>MRP/U</th>
                                @can('cost_price_show')
                                    <th>Total Cost Price</th>
                                @endcan
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

    <div class="modal fade" id="newDamageModal" tabindex="-1" aria-labelledby="newDamageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="min-width: 50% !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="margin-left: 41% !important" id="newDamageModalLabel">Customer Info
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form style="font-size: 12px;" enctype="multipart/form-data" id="newDamageForm">
                        <div class="container">
                            <div class="row">
                                <input type="hidden" name="inventory_id" id="inventory_id">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Product Name</label>
                                        <div class="input-group name">
                                            <input type="text"
                                                class="form-control {{ $errors->has('name') ? 'is-invalid' : 'is-valid-border' }}"
                                                name="name" value="{{ old('name') }}" id="product_name" placeholder="Name" readonly>
                                        </div>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <div class="input-group quantity">
                                            <input type="text"
                                                class="form-control {{ $errors->has('quantity') ? 'is-invalid' : 'is-valid-border' }}"
                                                placeholder="Enter Quantity" name="quantity"
                                                value="{{ old('quantity',0) }}">
                                        </div>
                                        @error('quantity')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="note">Note</label>
                                        <div class="input-group note">
                                            <textarea class="form-control is-valid-border" rows="3" name="note">Damage Product</textarea>
                                        </div>
                                        @error('note')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn btn-danger bg-gradient-danger btn-sm float-right"
                                data-dismiss="modal">Close</a>
                            <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-price" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Product Information</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-form" enctype="multipart/form-data" name="modal-form">
                        <input type="hidden" name="inventory_id" id="modal-inventory-id">
                        <input type="hidden" name="product_id" id="modal-product-id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-has-error">
                                    <label>Name</label>
                                    <input class="form-control" id="modal-name" disabled>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-group-has-error">
                                    <label>Selling Price</label>
                                    <input class="form-control" id="modal-selling-price" disabled>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-group-has-error">
                                    <label>Updated Selling Price</label>
                                    <input class="form-control" name="amount" placeholder="Enter Amount">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal-btn-price" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        $(function () {

            $('body').on('click', '.btn-price', function () {
                var inventoryId = $(this).data('inventory_id');
                var productId = $(this).data('product_id');
                var productName = $(this).data('product_name');
                var sellingPrice = $(this).data('selling_price');
                $('#modal-name').val(productName);
                $('#modal-selling-price').val(sellingPrice);
                $('#modal-inventory-id').val(inventoryId);
                $('#modal-product-id').val(productId);
                $('#modal-price').modal('show');
            });

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
                    url: "{{ route('purchase_inventory.datatable',['productId'=>request('productId')]) }}",
                    data: function(d) {
                        d.category = $('#category').val();
                        d.sub_category = $('#sub_category').val();
                        d.brand = $('#brand').val();
                        d.stock = $('#stock').val();
                    }
                },

                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1],[10, 25, 50, "All"]
                ],
                columns: [
                    { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
                    {data: 'product', name: 'product.name'},
                    {data: 'product_image', name: 'product_image',searchable: false},
                    {data: 'product_brand', name: 'product.brand.name'},
                    {data: 'product_code', name: 'product.code'},
                    {data: 'batch_no', name: 'batch_no'},
                    {data: 'product_category', name: 'product.category.name'},
                    {data: 'quantity', name: 'quantity'},
                    @can('cost_price_show')
                        {data: 'unit_price', name: 'unit_price'},
                    @endcan
                    {data: 'selling_price', name: 'selling_price'},
                    @can('cost_price_show')
                        {data: 'total_unit_price', name: 'total_unit_price'},
                    @endcan
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

            $('#modal-btn-price').click(function () {
                var formData = new FormData($('#modal-form')[0]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('inventory_price.update') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-price').modal('hide');
                            $('#modal-form')[0].reset();
                            Swal.fire(
                                'Price!',
                                response.message,
                                'success'
                            ).then((result) => {
                                table.ajax.reload();
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

            $('#stock').change(function(){
                table.ajax.reload();
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

            $(document).on('click','.btn-damage',function() {
                let inventory_id = $(this).data('id');
                let product_name = $(this).data('product');
                
                $('#inventory_id').val(inventory_id);
                $('#product_name').val(product_name);
                $('#newDamageModal').modal('show');
            });
            
            $('#newDamageForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#newDamageForm')[0]);
                $.ajax({
                    method: "Post",
                    url: "{{ route('damage.add') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {
                    if (response.success) {
                        $('#newDamageModal').modal('hide');
                        ajaxSuccessMessage(response.message)
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                });
            });

        })
    </script>
@endsection
