@extends('layouts.app')

@section('title')
    Bard Code
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('purchase_receipt.bar_code_print', ['order' => $order->id]) }}" class="btn btn-primary">Print</a>
                        </div>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Bar Code</th>
                            </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        @foreach($barCodes->chunk(6) as $row)
                                            <div class="r" style="clear: both">
                                                @foreach($row as $product)
                                                    <div style="width: 150px; margin: 10px; float: left; font-size: 12px" class="text-center">
                                                        {{ $product['name']}} <br>
                                                        <center>
                                                            {!! DNS1D::getBarcodeSVG($product['serial'], 'C128',1,25); !!}
                                                        </center>
                                                        {{-- {{ $product['serial'] }} --}}
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
