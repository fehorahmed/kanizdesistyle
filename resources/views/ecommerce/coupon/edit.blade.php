@extends('layouts.app')

@section('title')
    Coupon Edit
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
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Coupon Information</h3>
            </div>

            <form class="form-horizontal" method="POST" action="{{ route('ecom_coupon.edit', ['coupon'=>$coupon->id]) }}">
                @csrf
                <div class="card-body">

                    {{-- Code --}}
                    <div class="form-group row {{ $errors->has('code') ? 'has-error' : '' }}">
                        <label class="col-sm-2 col-form-label">Coupon Code <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="code" value="{{ old('code', $coupon->code) }}" class="form-control" placeholder="Enter coupon code">
                            @error('code')<span class="help-block text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control" placeholder="Optional description">{{ old('description', $coupon->description) }}</textarea>
                        </div>
                    </div>

                    {{-- Discount --}}
                    <div class="form-group row {{ $errors->has('discount') ? 'has-error' : '' }}">
                        <label class="col-sm-2 col-form-label">Discount <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" name="discount" value="{{ old('discount', $coupon->discount) }}" class="form-control" placeholder="Enter discount amount">
                            @error('discount')<span class="help-block text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Type --}}
                    <div class="form-group row {{ $errors->has('type') ? 'has-error' : '' }}">
                        <label class="col-sm-2 col-form-label">Type <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="type" class="form-control">
                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>Percent</option>
                            </select>
                            @error('type')<span class="help-block text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Minimum Purchase --}}
                    <div class="form-group row {{ $errors->has('min_purchase') ? 'has-error' : '' }}">
                        <label class="col-sm-2 col-form-label">Minimum Purchase <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" name="min_purchase" value="{{ old('min_purchase',$coupon->min_purchase) }}" class="form-control" placeholder="Enter minimum purchase amount">
                            @error('min_purchase')<span class="help-block text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Start Date --}}
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Start Date</label>
                        <div class="col-sm-10">
                            <input type="text" name="start_date" value="{{ old('start_date', date('d-m-Y',strtotime($coupon->start_date))) }}" class="form-control date-picker" autocomplete="off">
                        </div>
                    </div>

                    {{-- End Date --}}
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">End Date</label>
                        <div class="col-sm-10">
                            <input type="text" name="end_date" value="{{ old('end_date', date('d-m-Y',strtotime($coupon->end_date))) }}" class="form-control date-picker" autocomplete="off">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <div class="icheck-success d-inline">
                                <input type="radio" id="active" name="is_active" value="1" {{ old('is_active', $coupon->is_active) == 1 ? 'checked' : '' }}>
                                <label for="active">Active</label>
                            </div>
                            <div class="icheck-danger d-inline">
                                <input type="radio" id="inactive" name="is_active" value="0" {{ old('is_active', $coupon->is_active) == 0 ? 'checked' : '' }}>
                                <label for="inactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary bg-gradient-primary">Update</button>
                    <a href="{{ route('ecom_coupon') }}" class="btn btn-danger bg-gradient-danger float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
