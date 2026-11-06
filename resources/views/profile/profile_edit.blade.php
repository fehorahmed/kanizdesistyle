@extends('layouts.app')
@section('title','Profile Update')
@section('content')
    <div class="card card-default">
        <form method="post" enctype="multipart/form-data" action="{{ route('profile.edit_post') }}" class="mt-6 space-y-6">
            @csrf
            <div class="card-header">
                <div class="card-title">Profile Information</div>
            </div>
            <div class="card-body">
                <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                    <label for="name" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" value="{{ old('name',$user->name) }}" name="name" class="form-control" id="name"
                               placeholder="Enter Name">
                        @error('name')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('username') ? 'has-error' :'' }}">
                    <label for="username" class="col-sm-2 col-form-label">Username <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" value="{{ old('username',$user->username) }}" name="username" class="form-control" id="username"
                               placeholder="Enter Username">
                        @error('username')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                    <label for="mobile_no" class="col-sm-2 col-form-label">Mobile No.</label>
                    <div class="col-sm-10">
                        <input type="text" value="{{ old('username',$user->mobile_no) }}" name="mobile_no" class="form-control" id="mobile_no"
                               placeholder="Enter Mobile No.">
                        @error('mobile_no')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('email') ? 'has-error' :'' }}">
                    <label for="email" class="col-sm-2 col-form-label">Email  <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" value="{{ old('email',$user->email) }}" name="email" class="form-control"
                               id="email" placeholder="Enter Email">
                        @error('email')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div  class="form-group row {{ $errors->has('profile_photo') ? 'has-error' :'' }}">
                    <label for="profile_photo" class="col-sm-2 col-form-label">Profile Photo</label>
                    <div class="col-sm-10">
                        <input type="file" accept=".png, .jpg, .jpeg" name="profile_photo" class="form-control"
                               id="profile_photo">
                        @if($user->profile_photo)
                            <br>
                            <img height="80px" src="{{ asset($user->profile_photo) }}" alt="">
                        @endif
                        @error('profile_photo')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('signature_photo') ? 'has-error' :'' }}">
                    <label for="signature_photo" class="col-sm-2 col-form-label">Signature Photo</label>
                    <div class="col-sm-10">
                        <input type="file" accept=".png, .jpg, .jpeg" name="signature_photo" class="form-control"
                               id="signature_photo">
                        @if(file_exists($user->signature_photo))
                            <br>
                            <img height="50px" src="{{ asset($user->signature_photo) }}" alt="">
                        @endif
                        @error('signature_photo')
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
