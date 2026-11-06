@extends('layouts.app')
@section('title','Purchase Product Edit')
@section('style')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css" />
    <style>
         .col-form-label{
             font-size: 13px;
         }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Product Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('ecom_product.edit', ['product' => $product->id]) }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $product->name) : old('name') }}">
                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('slug') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Product Slug </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Slug"
                                       name="slug" value="{{ old('slug', $product->slug) }}">
                                @error('slug')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <input type="hidden" name="sister_concern_id" id="sister_concern_id" value="{{ auth()->user()->sister_concern_id }}"/>

                        <div class="form-group row {{ $errors->has('brand') ? 'has-error' :'' }}">
                            <label for="brand" class="col-sm-2 col-form-label">Brand</label>
                            <div class="col-sm-10">
                                <select name="brand" id="brand" class="form-control select2">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option {{ old('brand',$product->brand_id) == $brand->id ? 'selected' : '' }} value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('category') ? 'has-error' :'' }}">
                            <label for="category" class="col-sm-2 col-form-label">Category</label>
                            <div class="col-sm-10">
                                <select name="category" id="category" class="form-control select2">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option {{ old('category',$product->category_id) == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
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
                                        <option {{ old('unit',$product->unit_id) == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('code') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Code</label>

                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control" placeholder="Enter Code"
                                       name="code" value="{{ empty(old('code')) ? ($errors->has('code') ? '' : $product->code) : old('code') }}">

                                @error('code')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('alert_quantity') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Alert quantity</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter alert_quantity"
                                       name="alert_quantity" value="{{ old('alert_quantity', $product->alert_quantity) }}">

                                @error('alert_quantity')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('short_description') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Short Description</label>

                            <div class="col-sm-10">
                                <textarea name="short_description" class="form-control" id="short_description">{{ old('short_description',$product->short_description) }}</textarea>
                                @error('short_description')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('description') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Description</label>

                            <div class="col-sm-10">
                                <textarea name="description" class="form-control" id="editor">{{ old('description',$product->description) }}</textarea>
                                @error('description')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('image') ? 'has-error' :'' }}">
                            <label for="image" class="col-sm-2 col-form-label">Product Image(800x800)</label>
                            <div class="col-sm-10">
                                <input type="file" value="{{ old('image') }}" name="image" class="form-control" id="image">
                                <small class="text-red">Size will be less than 100kb</small>
                                @if(file_exists($product->image))
                                    <div id="image-preview-container">
                                        <img src="{{ asset($product->image) }}" height="80px" alt="">
                                        <button type="button" class="btn btn-danger btn-sm" id="btn-delete-image">
                                            Delete
                                        </button>
                                    </div>
                                @endif
                                @error('image')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Selling Price</label>
                            <div class="col-sm-10 form-group {{ $errors->has('selling_price') ? 'has-error' :'' }}">
                                <input type="number" class="form-control" placeholder="Enter Selling Price"
                                       name="selling_price" value="{{ old('selling_price',$product->old_selling_price) }}">
                                @error('selling_price')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Discount(%)</label>
                            <div class="col-sm-10 form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                <input type="number" class="form-control" placeholder="Enter Discount"
                                       name="discount" value="{{ old('discount',$product->discount_percent) }}">
                                @error('discount')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Youtube Video Link</label>
                            <div class="col-sm-10 form-group {{ $errors->has('youtube_video_link') ? 'has-error' :'' }}">
                                <input type="text" class="form-control" placeholder="Enter Youtube Video Link"
                                       name="youtube_video_link" value="{{ old('youtube_video_link',$product->youtube_video_link) }}">
                                @error('youtube_video_link')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Sort</label>
                            <div class="col-sm-10 form-group {{ $errors->has('sort') ? 'has-error' :'' }}">
                                <input type="number" class="form-control" placeholder="Sort"
                                       name="sort" value="{{ old('sort',$product->sort) }}">
                                @error('sort')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="form-group row {{ $errors->has('is_used') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Used Product <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="used" name="is_used" value="1" {{ old('is_used',$product->is_used) == '1' ? 'checked' : '' }}>
                                    <label for="used">
                                        Yes
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="notused" name="is_used" value="0" {{ old('is_used',$product->is_used) == '0' ? 'checked' : '' }}>
                                    <label for="notused">
                                        No
                                    </label>
                                </div>

                                @error('is_used')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="form-group row {{ $errors->has('is_featured') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Featured <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="featured" name="is_featured" value="1" {{ old('is_featured',$product->is_featured) == '1' ? 'checked' : '' }}>
                                    <label for="featured">
                                        Yes
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="notfeatured" name="is_featured" value="0" {{ old('is_featured',$product->is_featured) == '0' ? 'checked' : '' }}>
                                    <label for="notfeatured">
                                        No
                                    </label>
                                </div>

                                @error('is_featured')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('is_trending') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Trending <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="trending" name="is_trending" value="1" {{ old('is_trending',$product->is_trending) == '1' ? 'checked' : '' }}>
                                    <label for="trending">
                                        Yes
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="nottrending" name="is_trending" value="0" {{ old('is_trending',$product->is_trending) == '0' ? 'checked' : '' }}>
                                    <label for="nottrending">
                                        No
                                    </label>
                                </div>

                                @error('is_trending')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('is_best_selling') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Best Selling <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="best_selling" name="is_best_selling" value="1" {{ old('is_best_selling',$product->is_best_selling) == '1' ? 'checked' : '' }}>
                                    <label for="best_selling">
                                        Yes
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="not_best_selling" name="is_best_selling" value="0" {{ old('is_best_selling',$product->is_best_selling) == '0' ? 'checked' : '' }}>
                                    <label for="not_best_selling">
                                        No
                                    </label>
                                </div>

                                @error('is_best_selling')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('is_new_arrival') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is New Arrival <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="new_arrival" name="is_new_arrival" value="1" {{ old('is_new_arrival',$product->is_new_arrival) == '1' ? 'checked' : '' }}>
                                    <label for="new_arrival">
                                        Yes
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="not_new_arrival" name="is_new_arrival" value="0" {{ old('is_new_arrival',$product->is_new_arrival) == '0' ? 'checked' : '' }}>
                                    <label for="not_new_arrival">
                                        No
                                    </label>
                                </div>

                                @error('is_new_arrival')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('is_show') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Show <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="is_show" name="is_show" value="1" {{ old('is_show',$product->is_show) == '1' ? 'checked' : '' }}>
                                    <label for="is_show">
                                        Yes
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="not_is_show" name="is_show" value="0" {{ old('is_show',$product->is_show) == '0' ? 'checked' : '' }}>
                                    <label for="not_is_show">
                                        No
                                    </label>
                                </div>

                                @error('is_show')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline pull-right">
                                    <input checked type="radio" id="active" name="status" value="1" {{ old('status',$product->status) == '1' ? 'checked' : '' }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>
                                <div class="icheck-danger d-inline pull-right">
                                    <input type="radio" id="inactive" name="status" value="0" {{ old('status',$product->status) == '0' ? 'checked' : '' }}>
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
                        <a href="{{ route('ecom_product') }}" class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.umd.js"></script>
    <script>
        const {
            ClassicEditor,
            Essentials,
            Bold,
            Italic,
            Underline,      // Free
            Strikethrough,  // Free
            Code,           // Free
            Subscript,      // Free
            Superscript,    // Free
            Paragraph,
            Heading,        // Free (H1, H2, H3)
            List,           // Bulleted & Numbered
            Link,
            BlockQuote,     // Block Quote
            Table,
            TableToolbar,
            TableProperties,      // Free (Table color, border)
            TableCellProperties,  // Free (Cell color, padding)
            Alignment,      // Free (Left, Right, Center, Justify)
            Image,
            ImageToolbar,
            ImageCaption,
            ImageStyle,
            ImageUpload,
            ImageResize,
            SimpleUploadAdapter,
            MediaEmbed,     // Free (Youtube, Vimeo link embed)
            Indent,         // Free
            IndentBlock,    // Free
            Font            // Free (Font size, color, background)
        } = CKEDITOR;

        ClassicEditor
            .create(document.querySelector('#editor'), {
                plugins: [
                    Essentials, Paragraph, Heading, Bold, Italic, Underline, Strikethrough,
                    Code, Subscript, Superscript, List, Link, BlockQuote, 
                    Table, TableToolbar, TableProperties, TableCellProperties,
                    Alignment, Image, ImageToolbar, ImageCaption, ImageStyle, 
                    ImageUpload, ImageResize, SimpleUploadAdapter, MediaEmbed,
                    Indent, IndentBlock, Font
                ],
                toolbar: [
                    'undo', 'redo', '|', 
                    'heading', '|', 
                    'bold', 'italic', 'underline', 'strikethrough', 'code', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                    'alignment', '|',
                    'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                    'link', 'uploadImage', 'insertTable', 'blockQuote', 'mediaEmbed'
                ],
                table: {
                    contentToolbar: [
                        'tableColumn', 'tableRow', 'mergeTableCells', 
                        'tableProperties', 'tableCellProperties' // Advanced Free Features
                    ]
                },
                image: {
                    toolbar: [
                        'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', 
                        '|', 'toggleImageCaption', 'imageTextAlternative'
                    ],
                    resizeUnit: '%'
                },
                simpleUpload: {
                    uploadUrl: "{{ route('admin.ckeditor.upload', ['_token' => csrf_token() ]) }}",
                }
            })
            .then(editor => {
                console.log('All free features initialized');
            })
            .catch(error => {
                console.error(error);
            });
    </script>
    <script>
        $(function(){
            $('#btn-delete-image').click(function() {
                Swal.fire({
                    title: 'আপনি কি নিশ্চিত?',
                    text: "ছবিটি চিরতরে ডিলিট হয়ে যাবে!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'হ্যাঁ, ডিলিট করুন!',
                    cancelButtonText: 'না'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "POST",
                            url: "{{ route('ecom_product.delete_image', ['product' => $product->id]) }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: "DELETE"
                            },
                            success: function(response) {
                                if (response.success) {
                                    // পেজ রিফ্রেশ না করেই ইমেজ এবং বাটন সরিয়ে ফেলা
                                    $('#image-preview-container').fadeOut(500, function() {
                                        $(this).html('');
                                    });
                                    Swal.fire('Deleted!', response.message, 'success');

                                } else {
                                    Swal.fire('Error!', 'কিছু একটা ভুল হয়েছে।', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'সার্ভারে সংযোগ দেওয়া যাচ্ছে না।', 'error');
                            }
                        });
                    }
                });
            });


            let selected_sub_category = '{{ old('sub_category',$product->sub_category_id) }}';
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