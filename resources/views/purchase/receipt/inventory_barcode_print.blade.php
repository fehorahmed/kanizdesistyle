<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>{{ config('app.name', 'Laravel') }}</title> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 38mm 25mm;
            margin: 0 !important;
        }
        .barcode{
            text-align: center !important;
            font-size: 11px;
            font-weight: bold;
            margin-top: 7px !important;
        }
        p{
            font-size: 8px;
            margin: 0 !important;
            padding: 0 !important;
        }
        @media print {
            body,html{
                font-family: "Roboto", sans-serif;
                font-weight: 700;
                font-style: normal;
            }
            #code{
                font-size: 10px;
            }
            .barcode { page-break-before: always; } /* page-break-after works, as well */
        }
    </style>
</head>
<body>
<?php
$number= array("1", "2", "3", "4", "5", "6", "7","8","9","0");
$alphabet= array("B", "L", "A", "C", "K", "H", "O","R","S","E");

?>
{{-- {{ dd($order->products) }} --}}
    @for ($i=0;$i<$purchaseInventory->quantity;$i++)
        <div class="barcode">
            <p style="line-height: 10px;margin-bottom: 2px !important;font-size:10px;margin-top:10px !important;">
                @if (request('with_price') && request('with_price') == 1)
                    <span style="font-size: 10px !important;display: block;margin: 0 !important; padding-bottom: 5px !important;">Kaniz's Desi Style <br/></span>
                    {{-- @if ($purchaseInventory->product->category_id == 4) --}}
                        {{-- <span style="font-size: 10px !important;margin: 0 !important;">Size: {{ substr($purchaseInventory->product->name, -2) }}</span><br/> --}}
                        <span style="font-size: 10px !important;margin: 0 !important;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;display: inline-block;max-width: 1.5in;">{{ $purchaseInventory->product->name }}</span><br/>
                    {{-- @endif --}}
                    <span style="font-size: 10px !important;margin: 0 !important;">MRP: {{ number_format($purchaseInventory->selling_price,2) }} (Inc. VAT)</span>
                @else
                    <span style="font-size: 10px !important;display: block;margin: 0 !important; padding-bottom: 5px !important;">{{ $purchaseInventory->product->name }}</span>
                @endif
            </p>
            {!! DNS1D::getBarcodeSVG($purchaseInventory->serial_no.'-'.$purchaseInventory->batch_no, 'C93',1.0,40) !!}
            {{-- <p style="font-size:11px;display:block;font-weight: bold;">{{ $orderItem->serial_no }}</p> --}}
        </div>
    @endfor
<script>
    window.print();
    window.onafterprint = function(){ window.close()};
</script>
</body>
</html>
