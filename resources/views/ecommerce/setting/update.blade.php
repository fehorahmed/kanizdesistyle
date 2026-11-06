@extends('layouts.app')

@section('title')
    Setting Update
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
                    <h3 class="card-title">Setting Information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('ecom_settings.update',['setting'=>$setting->id]) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('banner_image_1') ? 'has-error' :'' }}">
                                    <label class="col-form-label">Banner Image1 (750x300px)</label>
                                    <input type="file" class="form-control" placeholder="Image"
                                                name="banner_image_1" value="{{ old('banner_image_1') }}">
                                    @if(file_exists($setting->banner_image_1))
                                        <a href="{{ asset($setting->banner_image_1) }}" download=""><img src="{{ asset($setting->banner_image_1) }}" height="80px" alt=""></a>
                                    @endif
                                    @error('banner_image_1')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Link</label>
                                    <input type="text" class="form-control" placeholder="Enter Banner Image Link"
                                            name="banner_image_link_1" value="{{ old('banner_image_link_1', $setting->banner_image_link_1) }}">
                                    @error('banner_image_link_1')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 29px;">
                                    <input type="checkbox" name="banner_image_status_1" value="1" {{ $setting->banner_image_status_1 == 1 ? 'checked' : '' }} data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('banner_image_2') ? 'has-error' :'' }}">
                                    <label class="col-form-label">Banner Image2 (750x300px)</label>
                                    <input type="file" class="form-control" placeholder="Image"
                                            name="banner_image_2" value="{{ old('banner_image_2') }}">
                                    @if(file_exists($setting->banner_image_2))
                                        <a href="{{ asset($setting->banner_image_2) }}" download=""><img src="{{ asset($setting->banner_image_2) }}" height="80px" alt=""></a>
                                    @endif
                                    @error('banner_image_2')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Link</label>
                                    <input type="text" class="form-control" placeholder="Enter Banner Image Link"
                                            name="banner_image_link_2" value="{{ old('banner_image_link_2', $setting->banner_image_link_2) }}">
                                    @error('banner_image_link_2')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 29px;">
                                    <input type="checkbox" name="banner_image_status_2" value="1" {{ $setting->banner_image_status_2 == 1 ? 'checked' : '' }} data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('banner_image_3') ? 'has-error' :'' }}">
                                    <label class="col-form-label">Banner Image3 (1530x300px)</label>
                                    <input type="file" class="form-control" placeholder="Image"
                                            name="banner_image_3" value="{{ old('banner_image_3') }}">
                                    @if(file_exists($setting->banner_image_3))
                                        <a href="{{ asset($setting->banner_image_3) }}" download=""><img src="{{ asset($setting->banner_image_3) }}" height="80px" alt=""></a>
                                    @endif
                                    @error('banner_image_3')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Link</label>
                                    <input type="text" class="form-control" placeholder="Enter Banner Image Link"
                                            name="banner_image_link_3" value="{{ old('banner_image_link_3', $setting->banner_image_link_3) }}">
                                    @error('banner_image_link_3')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 29px;">
                                    <input type="checkbox" name="banner_image_status_3" value="1" {{ $setting->banner_image_status_3 == 1 ? 'checked' : '' }} data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('banner_image_4') ? 'has-error' :'' }}">
                                    <label class="col-form-label">Banner Image4 (270x300px)</label>
                                    <input type="file" class="form-control" placeholder="Image"
                                            name="banner_image_4" value="{{ old('banner_image_4') }}">
                                    @if(file_exists($setting->banner_image_4))
                                        <a href="{{ asset($setting->banner_image_4) }}" download=""><img src="{{ asset($setting->banner_image_4) }}" height="80px" alt=""></a>
                                    @endif
                                    @error('banner_image_4')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Link</label>
                                    <input type="text" class="form-control" placeholder="Enter Banner Image Link"
                                            name="banner_image_link_4" value="{{ old('banner_image_link_4', $setting->banner_image_link_4) }}">
                                    @error('banner_image_link_4')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 29px;">
                                    <input type="checkbox" name="banner_image_status_4" value="1" {{ $setting->banner_image_status_4 == 1 ? 'checked' : '' }} data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('banner_image_5') ? 'has-error' :'' }}">
                                    <label class="col-form-label">Banner Image5 (1188x140px)</label>
                                    <input type="file" class="form-control" placeholder="Image"
                                            name="banner_image_5" value="{{ old('banner_image_5') }}">
                                    @if(file_exists($setting->banner_image_5))
                                        <a href="{{ asset($setting->banner_image_5) }}" download=""><img src="{{ asset($setting->banner_image_5) }}" height="80px" alt=""></a>
                                    @endif
                                    @error('banner_image_5')
                                        <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Link</label>
                                    <input type="text" class="form-control" placeholder="Enter Banner Image Link"
                                            name="banner_image_link_5" value="{{ old('banner_image_link_5', $setting->banner_image_link_5) }}">
                                    @error('banner_image_link_5')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 29px;">
                                    <input type="checkbox" name="banner_image_status_5" value="1" {{ $setting->banner_image_status_5 == 1 ? 'checked' : '' }} data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('primary_color') ? 'has-error' :'' }}">
                                <label class="col-form-label">Primary Color</label>
                                <input 
                                    type="text" 
                                    id="primaryColorPicker" 
                                    name="primary_color"
                                    value="{{ old('primary_color', $setting->primary_color ?? '#70c332') }}"
                                    class="form-control"
                                />
                                @error('primary_color')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    $("#primaryColorPicker").spectrum({
        type: "component",            // text field + color picker button
        showInput: true,              // text box এ hex লিখতে পারবেন
        allowEmpty: false,
        preferredFormat: "hex",       // output হবে HEX format
        showPalette: true,            // custom palette দেখাবে
        showInitial: true,            // আগে সিলেক্ট করা color preview
        palette: [
            ["#70c332", "#ff0000", "#0000ff", "#000000", "#ffffff"]
        ],
        change: function(color) {
            console.log("Selected: " + color.toHexString());
        }
    });
});
</script>
@endsection
