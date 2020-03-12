<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order</title>
</head>
<style>
    @page {
        size: letter;
    }

    body {
        height: auto;
        margin: auto;
        font-size: 18px;
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

    .order_header {
        font-weight: bold;
    }

    .store_name {
        text-transform: uppercase;
    }

    .store {
        font-size: 15px;
        display: flex;
        justify-content: space-between;
        text-transform: uppercase;
        font-weight: normal;
        font-style: italic;
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
        width: 20%;
    }

    .spaced-header {
        text-align: end;
    }

    .spaced-fixed-width {
        width: 30%;
    }

    .column {
        display: flex;
        flex-direction: column;
    }

    .column h4 {
        margin-top: 0;
        font-weight: 400;
        text-transform: uppercase;
    }

    .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
    }

    .left-footer {
        display: flex;
        align-items: flex-end;
        flex-direction: column;
    }

    .total {
        font-size: 13px;
        font-weight: bold;
    }
</style>
@if (config('app.env') === 'local')

<body>
    @else

    <body onload="window.print()" onafterprint="window.close()">
        @endif

        <div class="order">
            <div class="order_header">
                <div class="barcode">
                    <img src="data:image/png;base64,{{ $code }}">
                </div>
                <div class="store">
                    <p>
                        {{ $order->created_at->format('m/d/Y H:i:s') }}
                    </p>
                    <p>
                        {{ $store->name }}
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
                <div class="space_between">
                    <p>
                        ONL
                    </p>
                    <p>
                        {{ $order->id }}
                    </p>
                    <p>
                        AT {{ $order->created_at->format('H:m:s') }}
                    </p>
                </div>
                <div class="space_between">
                    <p>
                        {{ $order->method }}
                    </p>
                    <p>
                        {{ $order->created_at->format('m/d/Y l') }}
                    </p>
                    <p>
                        LOC1
                    </p>
                </div>
            </div>
            <hr>
            <div class="space_between">
                <span>
                    UNPAID
                </span>
                <span>
                    SW-Sales Walk
                </span>
                <span>
                    Recipient" Rep:MAS
                </span>
            </div>
            <hr>
            @if($customer_billing_address)
            <div class="space_between">
                <div class="column">
                    <h4> {{ $customer_billing_address['first_name'] }} {{ $customer_billing_address['last_name'] }}</h4>
                    <span>{{ $customer_billing_address['customer_id'] }}</span>
                    <span>{{ $customer_billing_address['street'] }}</span>
                    <span>{{ $customer_billing_address['street2'] }} </span>
                    <span> Tel: {{ $customer_billing_address['phone'] }} </span>
                    <span> {{ $customer_billing_address['city'] }}
                        {{ $customer_billing_address['region']['code'] }}</span>
                    <span>{{ $customer_billing_address['postcode'] }}</span>
                </div>
                <div class="column">
                    @if( isset($order->delivery['address']) )
                    <h4> {{ $order->delivery['address']['first_name'] }} {{ $order->delivery['address']['last_name'] }}
                    </h4>
                    <span>{{ $order->delivery['address']['street'] }}</span>
                    <span>{{ $order->delivery['address']['street2'] }}</span>
                    <span>{{ $order->delivery['address']['city'] }}
                        {{ $order->delivery['address']['region']['code'] }}
                        {{ $order->delivery['address']['postcode'] }}</span>
                    @elseif(isset($order->delivery['store_pickup']))
                    <h4> {{ $order->delivery['store_pickup']['first_name'] }}
                        {{ $order->delivery['store_pickup']['last_name'] }}</h4>
                    <span>{{ $order->delivery['store_pickup']['street'] }}</span>
                    <span>{{ $order->delivery['store_pickup']['street1']}}</span>
                    @endif
                </div>
            </div>
            <br>
            @endif
            <table class="no-border products-table">
                <thead>
                    <tr>
                        <th>
                            Product Code
                        </th>
                        <th>
                            Product Description
                        </th>
                        <th style="text-align: center;">
                            Units
                        </th>
                        <th>
                            Price
                        </th>
                        <th>
                            Ext. Price
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item )
                    <tr>
                        <td>{{ $item['sku'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td style="text-align: center;">{{ $item['qty'] }}</td>
                        <td>${{ $item['price']['amount'] }}</td>
                        <td>${{ $item['price']['amount'] * $item['qty'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <div class="space_between">
                <span>
                    mdse: ${{ $moneyFormatter->format($order->mdse_price) }}
                </span>
                <span>
                    delv: ${{ $moneyFormatter->format($order->delivery_fees_price) }}
                </span>
                <span>
                    svc/rel: $ .00
                </span>
                <span>
                    tax: ${{ $moneyFormatter->format($order->tax_price) }}
                </span>
                <span>
                    TOTAL: ${{ $moneyFormatter->format($order->total_price) }}
                </span>
            </div>
            <hr>
            <h2>
                Scan here if order filled form stock {{ $order->id }}
            </h2>
            <div class="footer">
                <div class="barcode">
                    <img src="data:image/png;base64,{{ $code }}">
                </div>
            </div>
    </body>

</html>