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
        width: 20%;
    }

    .spaced-header {
        text-align: end;
    }

    .spaced-fixed-width {
        width: 30%;
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
            @if($customer_billing_address)
            <table class="no-border">
                <tbody>
                    <tr>
                        <td class="small-header">Sold To:</td>
                        <td>{{ $customer_billing_address['customer_id'] }}</td>
                        <td class="spaced-header">Send To:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $customer_billing_address['street'] }}</td>
                        <td class="spaced-header"></td>
                        @if( isset($order->delivery['address']) )
                        <td class="spaced-fixed-width">{{ $order->delivery['address']['street'] }}</td>
                        @elseif(isset($order->delivery['store_pickup']))
                        <td class="spaced-fixed-width">{{ $order->delivery['store_pickup']['street'] }}</td>
                        @else
                        <td class="spaced-fixed-width"></td>
                        @endif
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $customer_billing_address['street2'] }}</td>
                        <td class="spaced-header"></td>
                        @if( isset($order->delivery['address']) )
                        <td class="spaced-fixed-width">{{ $order->delivery['address']['street2'] }}</td>
                        @elseif(isset($order->delivery['store_pickup']))
                        <td class="spaced-fixed-width">{{ $order->delivery['store_pickup']['street1'] }}</td>
                        @else
                        <td class="spaced-fixed-width"></td>
                        @endif
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $customer_billing_address['city'] }} {{ $customer_billing_address['region']['code'] }}
                            {{ $customer_billing_address['postcode'] }}</td>
                        <td class="spaced-header"></td>
                        @if( isset($order->delivery['address']) )
                        <td class="spaced-fixed-width">{{ $order->delivery['address']['city'] }}
                            {{ $order->delivery['address']['region']['code'] }}
                            {{ $order->delivery['address']['postcode'] }}</td>
                        @else
                        <td class="spaced-fixed-width"></td>
                        @endif
                    </tr>
                    <tr>
                        <td class="small-header"></td>
                        <td>{{ $customer_billing_address['phone'] }}</td>
                        <td class="spaced-header"></td>
                        <td class="spaced-fixed-width"></td>
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
                        <td>{{ $order->createdBy->name }} </td>
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
                        <td>{{ $item['price']['amount'] }}</td>
                        <td>{{ $item['price']['amount'] * $item['qty'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="total">
                <span>Mdse Amount: ${{ $moneyFormatter->format($order->mdse_price) }}</span>
                <span>Sales Tax: ${{ $moneyFormatter->format($order->tax_price) }}</span>
            </div>
            <p style="text-align:end;">Invoice Total: ${{ $moneyFormatter->format($order->total_price) }}</p>
            <br>
            <p style="text-align:end;">Net Invoice Total: ${{ $moneyFormatter->format($order->total_price) }}</p>
            <br>
            <span>Signed By:___________________</span>
        </div>
    </body>

</html>