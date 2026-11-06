@extends('layouts.app')
@section('title','Password Change')
@section('content')
    <div class="card card-default">
        <form method="post" action="{{ route('profile.password_change') }}" class="mt-6 space-y-6">
            @csrf
            <div class="card-header">
                <div class="card-title">Password Information</div>
            </div>
            <div class="card-body">
                <div class="form-group row {{ $errors->has('current_password') ? 'has-error' :'' }}">
                    <label for="current_password" class="col-sm-2 col-form-label">Current Password <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="password" autocomplete="current-password"  name="current_password" class="form-control" id="current_password"
                               placeholder="Current Password">
                        @error('current_password')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('password') ? 'has-error' :'' }}">
                    <label for="password" class="col-sm-2 col-form-label">New Password <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="password" autocomplete="new-password"  name="password" class="form-control" id="password"
                               placeholder="New Password">
                        @error('password')
                        <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('password_confirmation') ? 'has-error' :'' }}">
                    <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="password" autocomplete="new-password"  name="password_confirmation" class="form-control" id="password_confirmation"
                               placeholder="Confirm Password">
                        @error('password_confirmation')
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
