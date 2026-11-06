@extends('layouts.app')
@section('title','Account Head Sub Type')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header">
                <a href="{{ route('account_head.sub_type.add') }}" class="btn btn-primary bg-gradient-primary btn-sm">Account Head Sub Type Create <i class="fa fa-plus"></i></a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Account Head Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($subTypes as $type)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $type->name }}</td>
                                    <td>{{ $type->accountHeadType->name }}</td>
                                    <td>
                                        @if ($type->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('account_head.sub_type.edit', ['subType' => $type->id]) }}">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $(function () {
            $('#table').DataTable();
        })
    </script>
@endsection
