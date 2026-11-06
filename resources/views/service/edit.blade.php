@extends('layouts.app')
@section('title','Service Edit')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Service Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('service.edit',['service'=>$service->id]) }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $service->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('department') ? 'has-error' :'' }}">
                            <label for="department" class="col-sm-2 col-form-label">Department</label>
                            <div class="col-sm-10">
                                <select name="department" id="department" class="form-control select2">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option {{ old('department',$service->department_id) == $department->id ? 'selected' : '' }} value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('code') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Code</label>

                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control" placeholder="Enter Code" name="code" value="{{ empty(old('code')) ? ($errors->has('code') ? '' : $service->code) : old('code') }}">
                                @error('code')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('service_cost_price') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Service Cost Price *</label>

                            <div class="col-sm-10">
                                <input type="number" class="form-control" placeholder="Enter service cost" name="service_cost_price" value="{{ old('service_cost_price',$service->service_cost_price) }}">
                                @error('service_cost_price')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('service_sale_price') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Service Sale Price *</label>

                            <div class="col-sm-10">
                                <input type="number" class="form-control" placeholder="Enter service cost" name="service_sale_price" value="{{ old('service_sale_price',$service->service_sale_price) }}">
                                @error('service_sale_price')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('description') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Description</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Description"
                                       name="description" value="{{ empty(old('description')) ? ($errors->has('description') ? '' : $service->description) : old('description') }}">

                                @error('description')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline pull-right">
                                    <input checked type="radio" id="active" name="status" value="1" {{ old('status',$service->status) == '1' ? 'checked' : '' }}>
                                    <label for="active">
                                        Active
                                    </label>
                                </div>
                                <div class="icheck-danger d-inline pull-right">
                                    <input type="radio" id="inactive" name="status" value="0" {{ old('status',$service->status) == '0' ? 'checked' : '' }}>
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
                        <a href="{{ route('service.index') }}" class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
