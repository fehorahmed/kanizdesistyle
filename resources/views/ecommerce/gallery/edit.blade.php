@extends('layouts.app')

@section('title')
   Gallery Edit
@endsection
@section('style')
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
                    <h3 class="card-title">Gallery Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('ecom_gallery.edit',['gallery'=>$gallery->id]) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Title </label>
                            <div class="col-sm-10 form-group {{ $errors->has('title') ? 'has-error' :'' }}">
                                <input type="text" class="form-control" placeholder="Title"
                                       name="title" value="{{ old('title',$gallery->title) }}">
                                @error('title')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Type </label>
                            <div class="col-sm-10">
                                <select class="form-control select2" id="gallery_type" name="type">
                                    <option value="1" {{ old('type',$gallery->type)==1?'selected':'' }}>Image Gallery</option>
                                    <option value="2" {{ old('type',$gallery->type)==2?'selected':'' }}>Video Gallery</option>
                                    <option value="3" {{ old('type',$gallery->type)==3?'selected':'' }}>Success Story</option>
                                </select>
                                @error('type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('image') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Image (962x550)</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" placeholder="ছবি"
                                       name="image" value="{{ old('image') }}">
                                @error('image')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div id="video_area" class="form-group row">
                            <label class="col-sm-2 col-form-label">Youtube Video</label>
                            <div class="col-sm-10 form-group {{ $errors->has('video_link') ? 'has-error' :'' }}">
                                <input type="text" class="form-control" placeholder="Youtube Video"
                                       name="video_link" value="{{ old('video_link',$gallery->video_url) }}">
                                @error('video_link')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('sort') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Sort</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" placeholder="Serial No"
                                       name="sort" value="{{ old('sort',$gallery->sort) }}">
                                @error('sort')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>

                            <div class="col-sm-10">

                                <div class="icheck-success d-inline">
                                    <input checked type="radio" id="active" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($gallery->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>

                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inactive" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($gallery->status == '0' ? 'checked' : '')) :
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
                        <a href="{{ route('ecom_gallery') }}" class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function () {
            $('#gallery_type').change(function(){
                let type = $('#gallery_type').val();
                if(type == 2 || type == 3){
                    $('#video_area').show();
                }else{
                    $('#video_area').hide();
                }
            });
            $('#gallery_type').trigger('change');
        });

    </script>
@endsection
