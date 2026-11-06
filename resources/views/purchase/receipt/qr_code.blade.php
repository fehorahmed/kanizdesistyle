@extends('layouts.app')

@section('title')
    QR Code
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('purchase_receipt.qr_code_print', ['order' => $order->id]) }}" class="btn btn-primary">Print</a>
                        </div>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>QR Code</th>
                            </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        @foreach($qrCodes->chunk(10) as $row)
                                            <div class="r" style="clear: both">
                                                @foreach($row as $product)
                                                    <div style="width: 50px; margin: 10px; float: left; font-size: 8px" class="text-center">
                                                        {{ $product['name']}} <br>
                                                        {!! QrCode::size(50)->generate($product['serial']); !!}
                                                        <br>
                                                        {{ $product['serial'] }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
