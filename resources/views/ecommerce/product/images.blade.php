@extends('layouts.app')
@section('title','Product Images')

@section('style')
<style>
    .hidden {
        display: none !important;
    }
    .block__list img {
        /* height: 100px; */
    }
    .block__list_tags li {
        display: inline-block;
        text-align: center;
    }
    .block__list li {
        width: 9.8%;
        list-style: none;
        cursor: move;
    }
    .img-thumbnail {
        background-color: #fff !important;
    }
    .img-thumbnail {
        padding: 4px;
        line-height: 1.42857143;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
        transition: all .2s ease-in-out;
        display: inline-block;
        max-width: 100%;
        height: auto;
    }
    .btnRemoveImage {
        color: red !important;
        cursor: pointer !important;
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
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('ecom_product.images', ['product' => $product->id]) }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Product Name</th>
                                    <td>{{ $product->name }}</td>
                                    <th>Product Code</th>
                                    <td>{{ $product->code }}</td>
                                </tr>
                                <tr>
                                    <th>Brand</th>
                                    <td>{{ $product->brand->name??'' }}</td>
                                    <th>Category</th>
                                    <td>{{ $product->category->name??'' }}</td>
                                </tr>
                            </table>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info" id="btnUploadImages">Upload Images</button>
                                <input type="file" class="hidden" multiple id="inputImages">
                                <input type="hidden" id="product_id" value="{{ $product->id }}">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div style="width: 100%;height: auto;border: 3px dotted {{ $errors->has('imagesId') ? 'red' :'black' }};padding: 20px;">
                                    <div class="row" id="images" style="padding: 20px;">
                                        <span>Drag & Drop Images from your computer</span>
                                        <ul id="image-container" class="block__list block__list_tags">
                                            @if (old('imagesId') != null && sizeof(old('imagesId')) > 0)
                                                @foreach(old('imagesId') as $img)
                                                    <li>
                                                        <div class="image-item" style="margin-right: 10px">
                                                            <img class="img-thumbnail img" style="margin-bottom: 10px"
                                                                 src="{{ asset(old('imagesSrc.'.$loop->index)) }}">
                                                            <a class="btnRemoveImage">Remove</a>

                                                            <input class="inputImageId" type="hidden" name="imagesId[]" value="{{ $img }}">
                                                            <input class="inputImageSrc" type="hidden" name="imagesSrc[]" value="{{ old('imagesSrc.'.$loop->index) }}">
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @else
                                                @if (sizeof($product->images) > 0)
                                                    @foreach($product->images as $img)
                                                        <li class="img-item">
                                                            <div class="image-item">
                                                                <img class="img-thumbnail img" style="margin-bottom: 10px"
                                                                     src="{{ asset($img->path) }}">
                                                                <a class="btnRemoveImage">Remove</a>

                                                                <input class="inputImageId" type="hidden" name="imagesId[]" value="{{ $img->id }}">
                                                                <input class="inputImageSrc" type="hidden" name="imagesSrc[]" value="{{ $img->path }}">
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('imagesId')
                        <span class="help-block text-red">{{ $message }}</span>
                        @enderror
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
    <template id="imageTemplate">
        <li>
            <div class="image-item" style="margin-right: 10px">
                <img class="img-thumbnail img" style="margin-bottom: 10px">
                <a class="btnRemoveImage">Remove</a>

                <input class="inputImageId" type="hidden" name="imagesId[]">
                <input class="inputImageSrc" type="hidden" name="imagesSrc[]">
            </div>
        </li>
    </template>
@endsection
@section('script')
    <!-- Sortable -->
    <script src="{{ asset('themes/backend/plugins/sortable/Sortable.min.js') }}"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Images
            var el = document.getElementById('image-container');
            Sortable.create(el, {
                group: "words",
                animation: 150,
            });

            $('#btnUploadImages').click(function (e) {
                e.preventDefault();

                $('#inputImages').click();
            });


            $('#images').on({
                'dragover dragenter': function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                },
                'drop': function(e) {
                    var dataTransfer =  e.originalEvent.dataTransfer;
                    if( dataTransfer && dataTransfer.files.length) {
                        e.preventDefault();
                        e.stopPropagation();
                        $.each( dataTransfer.files, function(i, file) {
                            let product_id = $('#product_id').val();
                            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

                            if (file.size > 1228800) {
                                alert('Max allowed image size is 1.2MB per image.')
                            } else if (!allowedTypes.includes(file.type)) {
                                alert('Only jpg, png, webp, and avif files are allowed.');
                            } else if ($(".image-container").length > 20) {
                                alert('Maximum 20 photos allows');
                            } else {
                                var xmlHttpRequest = new XMLHttpRequest();
                                xmlHttpRequest.open("POST", '{{ route('ecom_product.upload_images') }}', true);
                                var formData = new FormData();
                                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                                formData.append("file", file);
                                formData.append("product_id", product_id);
                                xmlHttpRequest.send(formData);

                                xmlHttpRequest.onreadystatechange = function() {
                                    if (xmlHttpRequest.readyState == XMLHttpRequest.DONE) {
                                        var response = JSON.parse(xmlHttpRequest.responseText);

                                        if (response.success) {
                                            var html = $('#imageTemplate').html();
                                            var item = $(html);
                                            item.find('.img').attr('src', response.data.fullPath);
                                            item.find('.inputImageId').val(response.data.id);
                                            item.find('.inputImageSrc').val(response.data.path);

                                            $('#image-container').append(item);
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            });

            $('#inputImages').change(function (e) {
                $.each(e.target.files, function (index, file) {
                    let product_id = $('#product_id').val();
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];

                    if (file.size > 1228800) {
                        alert('Max allowed image size is 1.2MB per image.')
                    } else if (!allowedTypes.includes(file.type)) {
                        alert('Only jpg, png, webp, and avif files are allowed.');
                    } else if ($(".image-container").length > 2) {
                        alert('Maximum 20 photos allows');
                    } else {
                        var xmlHttpRequest = new XMLHttpRequest();
                        xmlHttpRequest.open("POST", '{{ route('ecom_product.upload_images') }}', true);
                        var formData = new FormData();
                        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                        formData.append("file", file);
                        formData.append("product_id", product_id);
                        xmlHttpRequest.send(formData);

                        xmlHttpRequest.onreadystatechange = function() {
                            if (xmlHttpRequest.readyState == XMLHttpRequest.DONE) {
                                var response = JSON.parse(xmlHttpRequest.responseText);

                                if (response.success) {
                                    var html = $('#imageTemplate').html();
                                    var item = $(html);
                                    item.find('.img').attr('src', response.data.fullPath);
                                    item.find('.inputImageId').val(response.data.id);
                                    item.find('.inputImageSrc').val(response.data.path);

                                    $('#image-container').append(item);
                                }
                            }
                        }
                    }
                });

                $(this).val('');
            });

            $('body').on('click', '.btnRemoveImage', function () {
                $(this).closest('li').remove();
            });
        });
    </script>
@endsection