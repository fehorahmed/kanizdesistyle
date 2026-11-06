@extends('layouts.app')
@section('title','App Settings')
@section('content')
    <div class="card card-default">
        <form method="post" enctype="multipart/form-data" action="{{ route('global_settings') }}" class="mt-6 space-y-6">
            @csrf
            <div class="card-header">
                <div class="card-title">App Settings Information</div>
            </div>
            <div class="card-body">
                @if(session()->has('error'))
                    <h1 class="text-danger">{{ session('error') }}</h1>
                @endif
                <div class="form-group row {{ $errors->has('logo') ? 'has-error' :'' }}">
                    <label for="logo" class="col-sm-2 col-form-label">Logo<span class="text-danger">{{ !file_exists($logoPath) ? '*' : '' }}</span></label>
                    <div class="col-sm-10">
                        <input {{ !file_exists($logoPath) ? 'required' : '' }} type="file" accept="image/png" name="logo" class="form-control" id="logo">
                        @if(file_exists($logoPath))
                            <img  height="50px"  src="{{ asset($logoPath) }}" alt="">
                        @endif
                        @error('logo')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('app_name') ? 'has-error' :'' }}">
                    <label for="app_name" class="col-sm-2 col-form-label">App Name<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="app_name" value="{{ old('app_name',env('APP_NAME')) }}" placeholder="Enter App Name" class="form-control" id="app_name">
                        @error('app_name')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('app_address') ? 'has-error' :'' }}">
                    <label for="app_address" class="col-sm-2 col-form-label">App Address<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="app_address" value="{{ old('app_address',env('APP_ADDRESS')) }}" placeholder="Enter App Address" class="form-control" id="app_address">
                        @error('app_address')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('app_contact') ? 'has-error' :'' }}">
                    <label for="app_contact" class="col-sm-2 col-form-label">App Contact<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="app_contact" value="{{ old('app_contact',env('APP_CONTACT')) }}" placeholder="Enter App Contact" class="form-control" id="app_contact">
                        @error('app_contact')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('app_email') ? 'has-error' :'' }}">
                    <label for="app_email" class="col-sm-2 col-form-label">App Email<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="app_email" value="{{ old('app_email',env('APP_EMAIL')) }}" placeholder="Enter App Email" class="form-control" id="app_email">
                        @error('app_email')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('app_url') ? 'has-error' :'' }}">
                    <label for="app_url" class="col-sm-2 col-form-label">App Url<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="app_url" value="{{ old('app_url',env('APP_URL')) }}" placeholder="Enter App Url" class="form-control" id="app_url">
                        @error('app_url')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('asset_url') ? 'has-error' :'' }}">
                    <label for="asset_url" class="col-sm-2 col-form-label">Asset Url<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="asset_url" value="{{ old('asset_url',env('ASSET_URL')) }}" placeholder="Enter Asset Url" class="form-control" id="asset_url">
                        @error('asset_url')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('app_debug') ? 'has-error' :'' }}">
                    <label for="app_debug" class="col-sm-2 col-form-label">App Debug<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="app_debug" value="{{ old('app_debug',(env('APP_DEBUG') ? 'true' : 'false')) }}" placeholder="Enter App Debug" class="form-control" id="app_debug">
                        @error('app_debug')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
            </div>
        </form>
    </div>
@endsection
