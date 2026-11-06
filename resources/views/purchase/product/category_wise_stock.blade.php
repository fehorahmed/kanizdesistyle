@extends('layouts.app')
@section('title', 'Category wise Stock')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                {{-- <div class="card-header">
                    <a href="{{ route('service.create') }}" class="btn btn-primary bg-gradient-primary btn-sm">Service
                        Create <i class="fa fa-plus"></i></a>
                </div> --}}
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control select2" id="category">
                                    <option value="">Select Option</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body" id="html_view_area">
   
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function() {
            $('#category').change(function(){
                let category = $(this).val();
  
                if(category != ''){
                    $.ajax({
                        method: "GET",
                        url: '{{ route('get_sub_category_with_product') }}',
                        data: {category: category}
                    }).done(function(response){
                        if(response.status){
                            $('#html_view_area').html(response.html_view);
                        }
                    });
                }
            });
            
        });
    </script>
@endsection
