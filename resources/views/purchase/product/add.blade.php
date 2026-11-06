@extends('layouts.app')
@section('title','Purchase Product Add')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Product Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('product.add') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Product Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('slug') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Product Slug </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Slug"
                                       name="slug" value="{{ old('slug') }}">

                                @error('slug')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> 

                        <div class="form-group row {{ $errors->has('brand') ? 'has-error' :'' }}">
                            <label for="brand" class="col-sm-2 col-form-label">Brand</label>
                            <div class="col-sm-10">
                                <select name="brand" id="brand" class="form-control select2">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option {{ old('brand') == $brand->id ? 'selected' : '' }} value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('category') ? 'has-error' :'' }}">
                            <label for="category" class="col-sm-2 col-form-label">Category *</label>
                            <div class="col-sm-10">
                                <select name="category" id="category" class="form-control select2">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option {{ old('category') == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('sub_category') ? 'has-error' :'' }}">
                            <label for="sub_category" class="col-sm-2 col-form-label">SubCategory</label>
                            <div class="col-sm-10">
                                <select name="sub_category" id="sub_category" class="form-control select2">
                                    <option value="">Select SubCategory</option>
                                </select>
                                @error('sub_category')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('unit') ? 'has-error' :'' }}">
                            <label for="unit" class="col-sm-2 col-form-label">Unit <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="unit" id="unit" class="form-control select2">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option {{ old('unit') == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('selling_price') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Selling Price</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Selling Price"
                                       name="selling_price" value="{{ old('selling_price') }}">

                                @error('selling_price')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('alert_quantity') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Alert quantity</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter alert_quantity"
                                       name="alert_quantity" value="{{ old('alert_quantity', 0) }}">

                                @error('alert_quantity')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group d-none row {{ $errors->has('vat_percent') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Vat(%)</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Vat(%)"
                                       name="vat_percent" value="{{ old('vat_percent') }}">

                                @error('vat_percent')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('description') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Description</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Description"
                                       name="description" value="{{ old('description') }}">

                                @error('description')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('image') ? 'has-error' :'' }}">
                            <label for="image" class="col-sm-2 col-form-label">Product Image(800x800)</label>
                            <div class="col-sm-10">
                                <input type="file" name="image" class="form-control" id="image">
                                @error('image')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="active" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inactive" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                    <label for="inactive">
                                        Inactive
                                    </label>
                                </div>

                                @error('status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                     <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary">Save</button>
                        <a href="{{ route('product') }}" class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function(){
            let selected_sub_category = '{{ old('sub_category') }}';
            $('#category').change(function(){
                let category = $(this).val();
                $('#sub_category').html('<option value="">Select SubCategory</option>');
                if(category != ''){
                    $.ajax({
                        method: "GET",
                        url: '{{ route('get_product_sub_category') }}',
                        data: {category: category}
                    }).done(function(response){
                        if(response.status){
                            $.each(response.sub_categories,function(i,item){
                                if(item.id == selected_sub_category){
                                    $('#sub_category').append('<option value="'+ item.id +'" selected>'+ item.name +'</option>');
                                }else{
                                    $('#sub_category').append('<option value="'+ item.id +'">'+ item.name +'</option>');
                                }
                            });
                        }
                    });
                }
            });
            $('#category').trigger('change');

            function slugify(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/[\s\W-]+/g, '-') // Replace spaces and non-word characters with -
                    .replace(/^-+|-+$/g, ''); // Remove leading/trailing dashes
            }

            $(document).ready(function () {
                $('input[name="name"]').on('keyup change paste', function () {
                    var name = $(this).val();
                    var slug = slugify(name);
                    $('input[name="slug"]').val(slug);
                });
            });
        });
    </script>
@endsection