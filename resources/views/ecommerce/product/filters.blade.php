@extends('layouts.app')
@section('title', 'Product Filters')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h4>Product Filters: {{ $product->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('ecom_product.filters', ['product' => $product->id]) }}" method="POST">
                    @csrf
                    <div class="row">
                        @foreach($categoryFilters as $filter)
                            @php
                                $selectedValue = $productFilterValues->where('filter_id', $filter->id)->first();
                            @endphp
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ $filter->name }}</label>

                                @if(in_array($filter->type, ['select','radio','checkbox']))
                                    <select name="filters[{{ $filter->id }}]" class="form-control select2">
                                        <option value="">Select {{ $filter->name }}</option>
                                        @foreach($filter->options as $option)
                                            <option value="{{ $option->id }}" 
                                                {{ $selectedValue && $selectedValue->filter_option_id == $option->id ? 'selected' : '' }}>
                                                {{ $option->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $filter->type }}" 
                                        name="filters[{{ $filter->id }}]" 
                                        class="form-control"
                                        value="{{ $selectedValue->value ?? '' }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Save Filters</button>
                    <a href="{{ route('ecom_product') }}" class="btn btn-danger mt-3">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
