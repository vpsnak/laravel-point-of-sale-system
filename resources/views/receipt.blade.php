<!DOCTYPE html>
<html lang="en">
​

<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<style>
    body {
        padding: 0;
        margin: 0;
        width: 80mm;
        height: 297mm;
    }

    .products-table thead {
        margin-bottom: 3px;
    }

    .no-border {
        border-collapse: collapse;
        width: 100%;
    }

    .black-header {
        background-color: black;
        -webkit-print-color-adjust: exact;
        color: white;
        width: 100%;
    }

    th {
        font-weight: normal;
    }

    .logo img {
        height: auto;
        width: 100%;
    }

    .phone {
        text-align: center;
    }

    .address {
        text-transform: uppercase;
        display: flex;
        justify-content: space-between;
    }

    .small-header {
        width: 13mm;
        text-align: end;
    }

    .spaced-header {
        width: 15mm;
        text-align: end;
    }
</style>
@php
$paid_by_credit_card = false;
$total_credit_card_tot = null;
$date_splitted = explode(" ", $order->created_at);
@endphp

{{-- <body onload="window.print()" onafterprint="window.close()"> --}}

<body>
    <div class="receipt">
        <div class="logo">
            <img src="{{ asset('storage/img/plantshed_receipt.png') }}">
        </div>

        <div class="store">

            <span style="font-size:14px;">
                {{ $store->name }}
            </span>

            <p class="phone">
                {{ $store->phone }}
            </p>

            <div class="address">
                <p>
                    {{ $store->street }}
                </p>
                <p>
                    {{ $store->city }}
                    {{ $store->postcode }}
                </p>
            </div>
        </div>

        <div>--------------------------------------------------------</div>

        <table style="font-size:14px;">
            <tbody>
                <tr>
                    <td class="small-header">Reg:</td>
                    <td>{{ $cash_register->name }}</td>
                    <td class="spaced-header">Trans:</td>
                    <td>{{ $order->id }}</td>
                </tr>
                <tr>
                    <td class="small-header">Clrk:</td>
                    <td>{{ $created_by->name }}</td>
                    <td class="spaced-header">Date:</td>
                    <td>{{ $date_splitted[0] }}</td>
                </tr>
                <tr>
                    <td class="small-header">Oper:</td>
                    <td>{{ $created_by->name }}</td>
                    <td class="spaced-header">Time:</td>
                    <td>{{ $date_splitted[1] }}</td>
                </tr>
            </tbody>
        </table>

        <table class="no-border products-table">
            <thead class="black-header">
                <tr>
                    <th colspan="2">
                        Product Description
                    </th>
                    <th>
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
            <tbody>
                @foreach($order->items as $item )
                <tr>
                    <td colspan="2">{{ $item->name }}</td>
                    <td style="text-align: center;">{{ $item->qty }}</td>
                    <td style="text-align: start;">${{ $item->price }}</td>
                    <td style="text-align:end;">${{ $item->price * $item->qty }}</td>
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
            </tbody>
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
        <p style="text-align: center; font-size:14px;">*** Tendering detaile ***</p>
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
                <th scope="row" style="font-weight:normal; text-align: start;">Refunded
                    {{ $payment['payment_type']['name']}}</th>
                <td style="text-align: end;">${{$payment['amount']}}+</td>
                @else
                <th scope="row" style="font-weight:normal; text-align: start;">{{ $payment['payment_type']['name']}}
                </th>
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
                <td style="text-align: start;">{{$order->shipping_address->first_name}}
                    {{$order->shipping_address->last_name}}</td>
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
