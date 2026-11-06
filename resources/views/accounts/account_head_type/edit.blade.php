@extends('layouts.app')
@section('title','Account Head Type Edit')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Account Head Type Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('account_head.type.edit', ['type' => $type->id]) }}">
                    @csrf

                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="type">
                                    <option value="">Select Type</option>
                                    <option value="1" {{ empty(old('type')) ? ($errors->has('type') ? '' : ($type->transaction_type == '1' ? 'selected' : '')) :
                                            (old('type') == '1' ? 'selected' : '') }}>Receive</option>
                                    <option value="2" {{ empty(old('type')) ? ($errors->has('type') ? '' : ($type->transaction_type == '2' ? 'selected' : '')) :
                                            (old('type') == '2' ? 'selected' : '') }}>Payment</option>
                                </select>

                                @error('type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $type->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline pull-right">
                                    <input checked type="radio" id="active" name="status" value="1" {{ old('status',$type->status) == '1' ? 'checked' : '' }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>
                                <div class="icheck-danger d-inline pull-right">
                                    <input type="radio" id="inactive" name="status" value="0" {{ old('status',$type->status) == '0' ? 'checked' : '' }}>
                                    <label for="inactive">
                                        Inactive
                                    </label>
                                </div>
                                @error('status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save</button>
                            <a href="{{ route('account_head.type') }}" class="btn btn-danger bg-gradient-danger btn-sm float-right">Cancel</a>
                        </div>
                        <!-- /.card-footer -->
                </form>
            </div>
        </div>
    </div>
@endsection
