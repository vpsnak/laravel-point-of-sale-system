<!DOCTYPE html>

@php
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

$barcode = new BarcodeGenerator();
if ($order->id){
$barcode->setText(strval($order->id));
}
$barcode->setType(BarcodeGenerator::Code128);
$barcode->setScale(1);
$barcode->setThickness(25);
$barcode->setFontSize(10);
$code = $barcode->generate();
@endphp

<html lang="en">
​
<head>
    <meta charset="UTF-8">
    <title>Order</title>
</head>
<style>
    @page {
   size: 7in 9.25in;
   margin: 27mm 16mm 27mm 16mm;
}
    body {
        width: 700px;
        height: auto;
    }
    .products-table thead {
        margin-bottom: 3px;
    }

    .products-table th {
        border-bottom: 1px solid black;
        border-top: 1px solid black;
    }

    .no-border {
        border-collapse: collapse;
        width: 100%;
    }
    .order_header{
        font-weight: bold;
    }

    .store_name{
        text-transform: uppercase;
    }
    
    .store{
        display: flex;
        justify-content: space-between;
        text-transform: uppercase;
        font-weight: normal;
        font-style: italic;
    }
    .total {
        font-size: 13px;
        font-weight: bold;
        padding-top: 1rem;
        display: flex;
        align-items: flex-end;
        flex-direction: column;
    }

    .barcode {
        text-align: center;
    }

    th {
        text-align: left;
        font-weight: normal;
    }
    .phone {
        text-align: center;
    }

    .space_between {
        display: flex;
        justify-content: space-between;
    }

    .small-header {
        text-align: end;
    }

    .spaced-header {
        text-align: end;
    }
</style>
@if (config('app.env') === 'local')

<body>
    @else

    <body onload="window.print()" onafterprint="window.close()">
        @endif

        <div class="order">
            <div class="order_header">
                <span class="store_name">
                {{ $store->name }}
                </span>
                <div class="space_between">
                    <p>
                     Invoice: {{ $order->id }}
                    </p>
                    <p>
                      Requested: {{ $order->created_at->format('m/d/Y l') }}
                    </p>
                </div>
                <div class="barcode">
                    <img src="data:image/png;base64,{{ $code }}">
                </div>
                  <div class="store">
                    <p>
                     {{ $order->created_at->format('m/d/Y H:i:s') }}
                    </p>
                    <p>
                      {{ $store->street }}
                    </p>
                    <p>
                        {{ $store->city }}
                        {{ $store->postcode }}
                    </p>
                    <p>
                      {{ $store->phone }}
                    </p>
                </div>
            </div>
            @if($order->billing_address && $order->delivery['address'] )
             <table class="no-border">
                <tbody>
                    <tr>
                        <td class="small-header">Sold To:</td>
                        <td>{{ $order->billing_address['customer_id'] }}</td>
                        <td class="spaced-header">Send To:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $order->billing_address['street'] }}</td>
                        <td class="spaced-header"></td>
                        <td>{{ $order->delivery['address']['street'] }}</td>
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $order->billing_address['street2'] }}</td>
                        <td class="spaced-header"></td>
                        <td>{{ $order->delivery['address']['street2'] }}</td>
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $order->billing_address['city'] }} {{ $order->billing_address['region']['code'] }} {{ $order->billing_address['postcode'] }}</td>
                        <td class="spaced-header"></td>
                        <td>{{ $order->delivery['address']['city'] }} {{ $order->delivery['address']['region']['code'] }} {{ $order->delivery['address']['postcode'] }}</td>
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $order->billing_address['phone'] }}</td>
                        <td class="spaced-header"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <br>
            @endif
            <hr>
            <table class="no-border">
                <tbody>
                    <tr>
                        <td class="small-header">Type:</td>
                        <td>SO - Invoice</td>
                        <td class="spaced-header">Del.Type:</td>
                        <td>{{ $order->method }}</td>
                    </tr>
                    <tr>
                        <td class="small-header">Order Placed:</td>
                        <td>{{ $order->created_at->format('m/d/Y H:i:s') }}</td>
                        <td class="spaced-header">Ship Via:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="small-header">Ord Ref:</td>
                        <td></td>
                        <td class="spaced-header">Inst1:</td>
                        <td>{{ "BILLING ONLY" }}</td>
                    </tr>
                    <tr>
                        <td class="small-header">Sales Rep:</td>
                        <td>{{ $order->created_by->name }}</td>
                        <td class="spaced-header">Inst2:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="small-header">Terms:</td>
                        <td>{{ "Due Upon Recpt" }}</td>
                        <td class="spaced-header">Reference:</td>
                        <td>{{ "00995054 ST" }}</td>
                    </tr>
                </tbody>
            </table>
            <table class="no-border products-table">
                <thead>
                    <tr>
                        <th>
                            Item
                        </th>
                        <th>
                            Product
                        </th>
                        <th colspan="2">
                            Description
                        </th>
                        <th style="text-align: center;">
                            Units
                        </th>
                        <th>
                            Price
                        </th>
                        <th>
                            Extended
                        </th>
                    </tr>
                </thead>
                <tbody style="font-size:14px;">
                    @foreach($order->items as $item )
                    <tr>
                        <td></td>
                        <td>{{ $item['name'] }}</td>
                    @if(array_key_exists('notes', $item))
                        <td colspan="2">{{  $item['notes']  }}</td>
                    @else
                    <td colspan="2"></td>
                    @endif
                        <td style="text-align: center;">{{ $item['qty'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td >{{ $item['price'] * $item['qty'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="total">
            <span>Mdse Amount: ${{ $order->total_paid }}</span>
            <span>Sales Tax: ${{ $order->total - $order->total_without_tax }}</span>
            </div>
            <p style="text-align:end;">Invoice Total: ${{ $order->total }}</p>
            <br>
            <p style="text-align:end;">Net Invoice Total: ${{ $order->total}}</p>
            <br>
            <span>Signed By:___________________</span>
        </div>
    </body>
</html>
