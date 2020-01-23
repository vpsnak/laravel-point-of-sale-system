<!DOCTYPE html>
<html lang="en">
​

<head>
    <meta charset="UTF-8">
    {{-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> --}}
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    body {
        width: 80mm;
        height: 297mm;
    }

    /* @font-face {
        font-family: 'Times New Roman';
        font-style: normal;
        font-weight: normal;
        src: url(http://allfont.net/allfont.css?fonts=courier);
    } */
</style>
@php
$paid_by_credit_card = false;
$total_credit_card_tot = null;
$date = '';
$date = $order->created_at;
$date_splitted = explode(" ", $date);
@endphp

{{-- <body onload="window.print()" onafterprint="window.close()"> --}}
    <body>
    <div class="receipt" style=" background-color: white; width:auto; height:auto; font-weight:normal; ">
        <span style="font-size:14px;"> {{$store->name}} </span>
        <p class="phone" style="text-align: center; font-size:13px;">{{$store->phone}}</p>
        <table style="font-size:12px; display:inline-table; width:100%;">
            <th scope="row" style="text-transform:uppercase; font-weight:normal; float:left;">{{$store->street}} {{$store->postcode}}
            </th>
            <th scope="row" style="text-transform:uppercase; font-weight:normal; float:right;">{{ $store->city }}
                {{$store->postcode}}</th>
        </table>
        <div class="barcode" style="width:100%;">--------------------------------------------------------</div>
        <table style="font-size:14px; display: inline-table; width:60%; float:left;">
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Reg:</th>
                <td style="text-align:start;">{{$cash_register->name}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Clrk:</th>
                <td style="text-align:start;">{{$created_by->name}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Oper:</th>
                <td style="text-align:start;">{{$created_by->name}}</td>
            </tr>
            <table style="font-size:14px; display: inline-table; width:40%; float:right;">
                <tr>
                    <th scope="row" style="font-weight:normal; text-align: end;">Trans:</th>
                    <td style="text-align:start;">{{$order->id}}</td>
                </tr>
                <tr>
                    <th scope="row" style="font-weight:normal; text-align: end;">Date:</th>
                    <td style="text-align:start;">{{$date_splitted[0]}}</td>
                </tr>
                <tr>
                    <th scope="row" style="font-weight:normal; text-align: end;">Time:</th>
                    <td style="text-align:start;">{{$date_splitted[1]}}</td>
                </tr>
            </table>
        </table>
        <div class="barcode" style="width:100%;">--------------------------------------------------------</div>
        <table class="products" style="font-size:12px; display: inline-table; width: 100%;">
            <tr>
                <th scope="row" style="font-weight:normal; text-transform:uppercase; text-align: start; ">
                    Product Description
                </th>
                <th scope="row" style="font-weight:normal; text-transform:uppercase; text-align: start; ">
                    Units
                </th>
                <th scope="row" style="font-weight:normal; text-transform:uppercase; text-align: start; ">
                    Price
                </th>
                <th scope="row" style="font-weight:normal; text-transform:uppercase; text-align: start; ">
                    Extended
                </th>
            </tr>
            @foreach($order->items as $item )
            <tr>
            <td style="text-align: start;">{{ $item->name }}</td>
            <td style="text-align: center;">{{$item->qty}}</td>
            <td style="text-align: start;">${{$item->price}}</td>
            <td style="text-align:end;">${{$item->price * $item->qty}}</td>
            </tr>
            @if($item->discount_type)
            <tr>
                @if($item->discount_type === 'percentage')
                <td scope="row" style="font-weight:normal; text-align:start; ">
                    Discount Pot: {{$item->discount_amount}}%
                </td>
                @else
                <td scope="row" style="font-weight:normal; text-align:start; ">
                    Discount Pot: {{$item->discount_amount}}-
                </td>
                @endif
                 <td>
                </td>
                <td>
                </td>
                 <td scope="row" style="font-weight:normal; text-align:end; ">
                    ${{$item->price - $item->final_price}}-
                </td>
            </tr>
            @endif
            @if($item->notes)
            <tr>
                <td style="font-weight:normal; text-align:start;">
                    {{$item->notes}}
                </td>
            </tr>
            @endif
            @endforeach
        </table>
        <table style="font-size:14px; width:100%;">
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Sales tax:</th>
                <td style="text-align: end;"> ${{$order->total - $order->total_without_tax}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Total Ant:</th>
                <td style="text-align: end;">${{$order->total}}</td>
            </tr>
        </table>
        <p style="text-align: center; font-size:14px;">*** Tandering detaile ***</p>
        <table style="font-size:14px; width:100%;">
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Order Total is:</th>
                <td style="text-align: center;">${{$order->total}}</td>
            </tr>
        </table>
        @foreach(json_decode($order['payments'] , true) as $payment)
        <table style="font-size:14px; width:100%;">
            <tr>
                <th scope="row" style="font-weight:normal; text-align: start;">{{$date_splitted[0]}}</th>
                @if($payment['status'] === 'refunded')
                <th scope="row" style="font-weight:normal; text-align: start;">Refunded {{ $payment['payment_type']['name']}}</th>
                <td style="text-align: end;">${{$payment['amount']}}+</td>
                @else
                <th scope="row" style="font-weight:normal; text-align: start;">{{ $payment['payment_type']['name']}}</th>
                <td style="text-align: end;">${{$payment['amount']}}-</td>
                @endif
            </tr>
        </table>
        @php
        if($payment['payment_type']['type'] === 'card'){
            $paid_by_credit_card = true;
            $total_credit_card_tot += $payment['amount'];
        }
        @endphp
        @endforeach
        <table style="font-size:14px; width:100%;"><br>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: center;">*** Balance Remaining ***</th>
                <td style="text-align: end;">$0.00</td>
            </tr>
        </table>
        <table style="font-size:14px; width:100%;"><br>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Total Amt Tendered:</th>
            <td style="text-align: end;">${{$order->total_paid}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Customer Change:</th>
                <td style="text-align: end;">${{$order->change}}</td>
            </tr>
        </table>
        <br>
        @if($order->shipping_address)
        <table style="font-size:14px; width:100%;">
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Delv on:</th>
            <td style="text-align: start;">{{$order->delivery_date}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Delv to:</th>
                <td style="text-align: start;">{{$order->shipping_address->first_name}} {{$order->shipping_address->last_name}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">C/O:</th>
                <td style="text-align: start;">{{$order->customer->first_name}} {{$order->customer->last_name}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Address:</th>
                <td style="text-align: start;">{{$order->shipping_address->street}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Address:</th>
                <td style="text-align: start;">{{$order->shipping_address->street2}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">City, St:</th>
                <td style="text-align: start;">{{$order->shipping_address->city}}</td>
            </tr>
        </table>
        @endif
        @if($paid_by_credit_card)
        <p style="text-align: center;">------ Credit Card Information ------</p>
        <table style="font-size:14px; width:100%;">
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Cardholder Name:</th>
                <td style="text-align: start;">Niggatron</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Credit Cd No:</th>
                <td style="text-align: start;">1234 vi</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Telephone No:</th>
                <td style="text-align: start;">21052510235</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Credit Card Tot:</th>
                <td style="text-align: center;">{{$total_credit_card_tot}}</td>
            </tr>
        </table>
        @endif
        <p style="text-align:end;">(Customer's Copy)</p>
        <span>Signature:___________________</span>
        <p style="text-align: end;">(Merchant's Copy)</p>
        <p style="text-align: center;">PLEASE SEE POSTED POLICY REGARDING REFUNDS</p>
    </div>
</body>
​

</html>
