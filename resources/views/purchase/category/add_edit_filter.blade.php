@extends('layouts.app')
@section('title', 'Category Filters')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Assign Filters to: {{ $category->name }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('category.add_edit_filter',['category' => $category->id]) }}" method="POST">
            @csrf

            <div class="row">
                @foreach($filters as $filter)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="filters[]" value="{{ $filter->id }}"
                                   {{ in_array($filter->id, $selectedFilters) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $filter->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Filters</button>
        </form>
    </div>
</div>
@endsection
