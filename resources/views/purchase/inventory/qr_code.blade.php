@extends('layouts.app')

@section('title')
    QR Code - {{ $product->name }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Serial</th>
                                <th>Quantity</th>
                                <th>QR Code</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td>{{ $row->serial_no }}</td>
                                    <td>{{ $row->quantity }}</td>
                                    <td>
                                        {!! QrCode::size(50)->generate($row->serial_no); !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
