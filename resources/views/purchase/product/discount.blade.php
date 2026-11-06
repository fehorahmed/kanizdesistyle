@extends('layouts.app')
@section('title','Product Discount Settings')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Product Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('product.discount_settings') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('category') ? 'has-error' :'' }}">
                            <label for="category" class="col-sm-2 col-form-label">Category </label>
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
                        <div class="form-group row {{ $errors->has('product') ? 'has-error' :'' }}">
                            <label for="product" class="col-sm-2 col-form-label">Product(%)</label>
                            <div class="col-sm-10">
                                <select name="product[]" id="product" class="form-control select2" multiple>
                                    {{-- <option value="">All Product</option> --}}
                                </select>
                                @error('product')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('discount') ? 'has-error' :'' }}">
                            <label for="discount" class="col-sm-2 col-form-label">Discount</label>
                            <div class="col-sm-10">
                                <input type="text" name="discount"  value="0" class="form-control" placeholder="Enter Discount"/>
                                @error('discount')
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
                // $('#product').html('<option value="-1">All Product</option>');
                if(category != ''){
                    $.ajax({
                        method: "GET",
                        url: '{{ route('get_product_by_category') }}',
                        data: {category: category}
                    }).done(function(response){
                        if(response.status){
                            $.each(response.products,function(i,item){
                                if(item.id == selected_sub_category){
                                    $('#product').append('<option value="'+ item.id +'" selected>'+ item.name +'</option>');
                                }else{
                                    $('#product').append('<option value="'+ item.id +'">'+ item.name +'</option>');
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