<!DOCTYPE html>
<html lang="en">
​

<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
</head>
<style>
    body {
        padding: 0;
        margin: 0;
        width: 80mm;
        height: auto;
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

    .logo {
        text-align: center;
    }

    .logo img {
        height: auto;
        width: 65%;
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
@if (config('app.env') === 'local')

<body>
    @else

    <body onload="window.print()" onafterprint="window.close()">
        @endif

        <div class="receipt">
            <div class="logo">
                <img src="{{ asset('storage/img/plantshed_receipt.png') }}">
            </div>

            <div class="store">

                <span style="font-size:14px;">
                    {{ $store['name'] }}
                </span>

                <p class="phone">
                    {{ $store['phone'] }}
                </p>

                <div class="address">
                    <p>
                        {{ $store['street'] }}
                    </p>
                    <p>
                        {{ $store['city'] }}
                        {{ $store['postcode'] }}
                    </p>
                </div>
            </div>

            <div>--------------------------------------------------------</div>

            <table style="font-size:14px;">
                <tbody>
                    <tr>
                        <td class="small-header">Reg:</td>
                        <td>{{ $reg}}</td>
                        <td class="spaced-header">Trans:</td>
                        <td>{{ $trans }}</td>
                    </tr>
                    <tr>
                        <td class="small-header">Clrk:</td>
                        <td>{{ $clrk }}</td>
                        <td class="spaced-header">Date:</td>
                        <td>{{ $date }}</td>
                    </tr>
                    <tr>
                        <td class="small-header">Oper:</td>
                        <td>{{ $oper }}</td>
                        <td class="spaced-header">Time:</td>
                        <td>{{ $time }}</td>
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
                <tbody style="font-size:14px;">
                    @foreach($items as $item )
                    <tr>
                        <td colspan="2">{{ $item['name'] }}</td>
                        <td style="text-align: center;">{{ $item['qty'] }}</td>
                        <td>${{ $item['price']['amount'] }}</td>
                        <td style="text-align:end;">${{ $item['price']['amount'] * $item['qty'] }}</td>
                    </tr>
                    @if($item['discount']['type'])
                    <tr>
                        @if($item['discount']['type'] === 'percentage')
                        <td style="text-align:start;">
                            Discount: {{ $item['discount']['amount'] }}%
                        </td>
                        @else
                        <td>
                            Discount: {{ $item['discount']['amount'] }}-
                        </td>
                        @endif
                        <td>
                        </td>
                        <td>
                        </td>
                        <td style="text-align:end; ">
                            {{-- ${{ $item['price'] - $item['final_price'] }}- --}}
                        </td>
                    </tr>
                    @endif
                    @if(array_key_exists('notes', $item))
                    <tr>
                        <td>
                            {{ $item['notes'] }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <table style="font-size:14px;">
                @if($delivery_fees > 0)
                <tr>
                    <td style="text-align: end;">Delivery fees:</td>
                    <td style="text-align: end;"> ${{$delivery_fees }}</td>
                </tr>
                @endif
                <tr>
                    <td style="text-align: end;">Sales tax:</td>
                    <td style="text-align: end;"> ${{ $sales_tax }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Total Amt:</td>
                    <td style="text-align: end;">${{ $total_ant }}</td>
                </tr>
            </table>
            <p style="text-align: center; font-size:14px;">*** Tendering details ***</p>
            <table style="font-size:14px;">
                <tr>
                    <td style="text-align: end;">Order Total is:</th>
                    <td style="text-align: center;">${{ $total_ant }}</td>
                </tr>
            </table>

            @foreach ($payments as $payment)
            <table style="font-size:14px;">
                <tr>
                    <td>
                        {{ Carbon\Carbon::parse($payment['created_at'])->format('m/d/Y') }}</td>
                    @if($payment['status'] === 'refunded')
                    <td>
                        Refunded
                        {{ $payment['payment_type_name']}}
                    </td>
                    <td style="text-align: end;">${{$payment['price']['amount']}}+</td>
                    @else
                    <td>{{ $payment['payment_type_name'] }}</td>
                    <td style="text-align: end;">${{ $payment['price']['amount'] }}-</td>
                    @endif
                </tr>
                @if($payment['payment_type_name'] === 'card')
                <tr>
                    <td style="text-align: end;">Cardholder's Name:</td>
                    <td style="text-align: end;">{{ $payment['elavon_api_payments'][0]['card_holder'] }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Credit Cd No:</td>
                    <td style="text-align: end;">{{ $payment['elavon_api_payments'][0]['card_number'] }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Credit Card Tot:</td>
                    <td style="text-align: end;">{{ $payment['price']['amount'] }}</td>
                </tr>
                @endif
            </table>
            @endforeach
            <br>
            <table style="font-size:14px;">
                <tr>
                    <td style="text-align: center;">*** Balance Remaining ***</th>
                    <td style="text-align: end;">$ {{ $balance_remaining }}</td>
                </tr>
            </table>
            <br>
            <table style="font-size:14px;">
                <tr>
                    <th style="text-align: end;">Total Amt Tendered:</th>
                    <td style="text-align: end;">${{$total_amt_tendered}}</td>
                </tr>
                <tr>
                    <th style="text-align: end;">Customer Change:</th>
                    <td style="text-align: end;">${{$customer_change}}</td>
                </tr>
            </table>
            <br>
            @if($shipping_address)
            <table style="font-size:14px;">
                <tr>
                    <td style="text-align: end;">Delv on:</td>
                    <td>{{ $dlvr_on }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Delv to:</td>
                    <td>{{ $dlvr_to}}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: end;">C/O:</td>
                    <td>{{ $c_o }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Address:</td>
                    <td>{{ $address }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Address:</td>
                    <td>{{ $address_2 }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">City, St:</td>
                    <td>{{ $city }}</td>
                </tr>
            </table>
            @endif
            <p style="text-align:end;">(Customer's Copy)</p>
            <span>Signature:___________________</span>
            <p style="text-align: end;">(Merchant's Copy)</p>
            <p style="text-align: center;">PLEASE SEE POSTED POLICY REGARDING REFUNDS</p>
        </div>
    </body>

</html>