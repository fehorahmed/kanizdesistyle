@extends('layouts.app')

@section('title')
    Banner Add
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
                    <h3 class="card-title">Banner Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('ecom_banner.add') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Type</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="type">
                                    <option value="">Select Option</option>
                                    <option value="1" {{ old('type')==1?'selected':'' }}>Banner : Size (1976x688px)</option>
                                    {{-- <option value="2" {{ old('type')==2?'selected':'' }}>MD Banner : Size (1080x1080px)</option>
                                    <option value="3" {{ old('type')==3?'selected':'' }}>Group Banner : Size (2560x1705px)</option> --}}
                                </select>
                                @error('type')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('image') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Image </label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" placeholder="Image"
                                       name="image" value="{{ old('image') }}">
                                @error('image')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('sort') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Sort</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" placeholder="Serial No"
                                       name="sort" value="{{ old('sort',$maxSort) }}">
                                @error('sort')
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
                        <a href="{{ route('ecom_banner') }}" class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function () {
            
        });

    </script>
@endsection
