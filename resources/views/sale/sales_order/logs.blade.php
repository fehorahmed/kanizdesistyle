@extends('layouts.app')
@section('title', 'Sale Logs')
@section('style')
    <!-- Include DataTables CSS -->

@endsection
@section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-end">
                    {{-- <a href="{{ route('sale.list') }}" class="btn btn-primary">Sale List</a> --}}
                </div>
                <h4 class="page-title">Sale Order Logs</h4>
            </div>
            <!--end page-title-box-->
        </div>
        <!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h4 class="card-title">Striped rows</h4>
                    <p class="text-muted mb-0">
                        Use <code>.table-striped</code> to add zebra-striping to any table row
                        within the <code>&lt;tbody&gt;</code>.
                    </p>
                </div> --}}
                <!--end card-header-->
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <th>SL</th>
                            <th>Order NO.</th>
                            <th>Order Type</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Date</th>
                        </thead>
                        <tbody>
                            @foreach($logs as $index => $log)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $log->saleOrder->order_no??'' }}</td>
                                    <td>
                                        @if ($log->saleOrder->sale_type == 1)
                                            Offline
                                        @elseif ($log->saleOrder->sale_type == 2)
                                            Online
                                        @elseif ($log->saleOrder->sale_type == 3)
                                            Ecommerce
                                        @endif
                                    </td>
                                    <td>{{ $log->user->name??'' }}</td>
                                    <td>{{ $log->status }}</td>
                                    <td>{{ date('d-m-Y H:i:s',strtotime($log->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div> <!-- end col -->


    </div> <!-- end row -->
@endsection


@section('scripts')

@endsection
