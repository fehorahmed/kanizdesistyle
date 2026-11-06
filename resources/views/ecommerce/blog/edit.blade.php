@extends('layouts.app')

@section('title')
    Blog Edit
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/summernote/summernote-bs4.min.css') }}">
    <style>
        @font-face {
            font-family: 'Kalpurush';
            src: url('{{ asset('fonts/Kalpurush/kalpurush.ttf') }}') format('truetype');
        }
        .col-form-label{
            font-size: 13px;
        }
        .note-editable {
            font-family: 'Kalpurush', sans-serif;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Blog Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('ecom_blog.edit',['blog'=>$blog->id]) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('title') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Title</label>
                            <div class="col-sm-10">
                                <input type="text" name="title" class="form-control" value="{{ $blog->title }}" placeholder="Title">
                                @error('title')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row form-group {{ $errors->has('image') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Image(800x560px)</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" placeholder="Image"
                                       name="image" value="{{ old('image') }}">
                                @error('image')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row form-group {{ $errors->has('short_content') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Short Content</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="short_content" placeholder="Short Content">{{ old('short_content',$blog->short_content) }}</textarea>
                                @error('short_content')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row form-group {{ $errors->has('content') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Content</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="content" name="content" placeholder="Content">{{ $blog->content }}</textarea>
                                @error('content')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row form-group {{ $errors->has('sort') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Sort</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" placeholder="Sort"
                                       name="sort" value="{{ old('sort',$blog->sort) }}">
                                @error('sort')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>

                            <div class="col-sm-10">

                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="active" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($blog->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inactive" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($blog->status == '0' ? 'checked' : '')) :
                                            (old('status') == '0' ? 'checked' : '') }}>
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
                        <a href="{{ route('ecom_blog') }}" class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('themes/backend/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        $(function (){
            // Summernote
            $('#content').summernote({
                height: 300, // optional height
                fontNames: ['Arial', 'Kalpurush', 'Times New Roman', 'Courier New'],
                fontNamesIgnoreCheck: ['Kalpurush'],
                lineHeights: ['1', '1.2', '1.5', '2.0', '3.0'],
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                    ['misc', ['undo', 'redo']]
                ]
            });
        });
    </script>
@endsection
