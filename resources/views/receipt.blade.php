@php
use App\Order;
use App\Helper\Price;
@endphp

<!DOCTYPE html>
<html>
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
                        <td>{{ $order->createdBy->name }}</td>
                        <td class="spaced-header">Date:</td>
                        <td>{{ $order->created_at->format('m/d/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="small-header">Oper:</td>
                        <td>{{ $order->createdBy->name }}</td>
                        <td class="spaced-header">Time:</td>
                        <td>{{ $order->created_at->format('h:m:s') }}</td>
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
                    @foreach($order->items as $item )
                    <tr>
                        <td colspan="2">{{ $item['name'] }}</td>
                        <td style="text-align: center;">{{ $item['qty'] }}</td>
                        <td>{{ $moneyFormatter->format(Order::getItemPrice($item)) }}</td>
                        <td style="text-align:end;">{{ $moneyFormatter->format(Order::getItemTimesQtyPrice($item)) }}</td>
                    </tr>
                    @if($item['discount']['type'])
                    <tr>
                        @if($item['discount']['type'] === 'percentage')
                        <td style="text-align:start;">
                            Discount: {{ $item['discount']['amount'] }}%
                        </td>
                        @else
                        <td>
                            Discount: {{ $moneyFormatter->format(Price::parsePrice($item['discount']['amount'])) }}-
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
                @if($order->delivery_fees_price->isPositive())
                <tr>
                    <td style="text-align: end;">Delivery fees:</td>
                    <td style="text-align: end;">{{ $moneyFormatter->format($order->delivery_fees_price) }}</td>
                </tr>
                @endif
                <tr>
                    <td style="text-align: end;">Sales tax:</td>
                    <td style="text-align: end;">{{ $moneyFormatter->format($order->tax_price) }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Total Amt:</td>
                    <td style="text-align: end;">{{ $moneyFormatter->format($order->total_price) }}</td>
                </tr>
            </table>
            <p style="text-align: center; font-size:14px;">*** Tendering details ***</p>
            <table style="font-size:14px;">
                <tr>
                    <td style="text-align: end;">Order Total is:</th>
                    <td style="text-align: center;">{{ $moneyFormatter->format($order->total_price) }}</td>
                </tr>
            </table>
            @foreach($order->transactions as $transaction)

            @php
            if($transaction['payment_type_name'] === 'card' || $transaction['payment_type_name'] === 'pos-terminal')
            $payment['payment_type_name'] = 'Credit card';
            @endphp

            <table style="font-size:14px;">
                <tr>
                    <td>
                        {{ $transaction->created_at->format('m/d/Y h:m:s') }}
                    </td>
                    @if($transaction->refund)
                    <td>
                        Refunded
                        {{ $transaction['payment_type_name']}}
                    </td>
                    <td style="text-align: end;">{{ $moneyFormatter->format($transaction->price) }}+</td>
                    @else
                    <td>{{ $transaction['payment_type_name'] }}</td>
                    <td style="text-align: end;">{{ $moneyFormatter->format($transaction->price) }}-</td>
                    @endif
                </tr>
                @if($transaction['payment_type_name'] === 'card')
                <tr>
                    <td style="text-align: end;">Cardholder's Name:</td>
                    <td style="text-align: end;">{{ $transaction['elavon_api_payments'][0]['card_holder'] }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Credit Cd No:</td>
                    <td style="text-align: end;">{{ $transaction['elavon_api_payments'][0]['card_number'] }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Credit Card Tot:</td>
                    <td style="text-align: end;">{{ $moneyFormatter->format(Price::parsePrice($transaction['price'])) }}</td>
                </tr>
                @endif
            </table>
            @endforeach

            <br>

            <table style="font-size:14px;">
                <tr>
                    <td style="text-align: center;">*** Balance Remaining ***</th>
                    <td style="text-align: end;">{{ $moneyFormatter->format($order->remaining_price) }}</td>
                </tr>
            </table>
            <br>
            <table style="font-size:14px;">
                <tr>
                    <th style="text-align: end;">Total Amt Tendered:</th>
                    <td style="text-align: end;">{{ $moneyFormatter->format($order->income_price) }}</td>
                </tr>
                <tr>
                    <th style="text-align: end;">Customer Change:</th>
                    <td style="text-align: end;">{{ $moneyFormatter->format($order->remaining_price) }}</td>
                </tr>
            </table>

            <br>

            @if($order->delivery)
            <table style="font-size:14px;">
                <tr>
                    <td style="text-align: end;">Delv on:</td>
                    <td>{{ $order->delivery->date }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Delv to:</td>
                    <td>
                        {{ $order->delivery['address']['first_name'] }}
                        {{ $order->delivery['address']['last_name'] }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: end;">C/O:</td>
                    <td>{{ $order->customer->full_name }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Address:</td>
                    <td>{{ $order->delivery['address']['street'] }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">Address:</td>
                    <td>{{ $order->delivery['address']['street2'] }}</td>
                </tr>
                <tr>
                    <td style="text-align: end;">City, St:</td>
                    <td>{{ $order->delivery['address']['city'] }}</td>
                </tr>
            </table>
            @endif
            {{-- @if($paid_by_credit_card)
        <p style="text-align: center;">------ Credit Card Information ------</p>
        <table style="font-size:14px; width:100%;">
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Cardholder Name:</th>
                <td style="text-align: start;">{{$card_holder}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Credit Cd No:</th>
                <td style="text-align: start;">{{$card_number}}</td>
            </tr>
            <tr>
                <th scope="row" style="font-weight:normal; text-align: end;">Credit Card Tot:</th>
                <td style="text-align: center;">{{$total_credit_card_tot}}</td>
            </tr>
            </table>
            @endif --}}
            <p style="text-align:end;">(Customer's Copy)</p>
            <span>Signature:___________________</span>
            <p style="text-align: end;">(Merchant's Copy)</p>
            <p style="text-align: center;">PLEASE SEE POSTED POLICY REGARDING REFUNDS</p>
        </div>
    </body>
</html>
