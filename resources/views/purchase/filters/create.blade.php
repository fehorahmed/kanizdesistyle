@extends('layouts.app')

@section('title', isset($filter) ? 'Edit Filter' : 'Add Filter')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                {{ isset($filter) ? 'Edit Filter' : 'Add Filter' }}
            </div>
            <div class="card-body">
                <form action="{{ route('filters.store') }}" method="POST">
                    @csrf

                    <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                        <label for="name">Filter Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $filter->name ?? '') }}" required>
                        @error('name')
                            <span class="help-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Filter Type</label>
                        <select name="type" class="form-control" required>
                            @foreach(['checkbox','text','number','select','radio'] as $type)
                                <option value="{{ $type }}" {{ old('type', $filter->type ?? '') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="options">Options (for select, radio, checkbox)</label>
                        <div id="options-wrapper">
                            @php
                                $options = old('options', $filter->options ?? []);
                                if(is_string($options)) {
                                    $options = json_decode($options, true) ?: [];
                                }
                            @endphp

                            @foreach($options as $option)
                                <div class="option-input mb-2">
                                    <input type="text" name="options[]" class="form-control d-inline-block w-75" value="{{ $option }}">
                                    <button type="button" class="btn btn-danger remove-option">Remove</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-primary mt-2" id="add-option">Add Option</button>
                    </div>

                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#add-option').click(function() {
        $('#options-wrapper').append(`
            <div class="option-input mb-2">
                <input type="text" name="options[]" class="form-control d-inline-block w-75">
                <button type="button" class="btn btn-danger remove-option">Remove</button>
            </div>
        `);
    });

    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-input').remove();
    });
});
</script>
@endsection
