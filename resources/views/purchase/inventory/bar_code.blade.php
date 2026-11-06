@extends('layouts.app')

@section('title')
    Bar Code - {{ $product->name }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Serial</th>
                                <th>Quantity</th>
                                <th>Bar Code</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td>{{ $row->serial_no }}</td>
                                    <td>{{ $row->quantity }}</td>
                                    <td>
                                        {{-- {!! QrCode::size(50)->generate($row->serial_no); !!} --}}
                                        {!! DNS1D::getBarcodeSVG($row->serial_no, 'C128',1,25); !!}
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
