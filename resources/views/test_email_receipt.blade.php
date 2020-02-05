<html>

<body>
    <div style="font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;">
        <table cellspacing="0" cellpadding="0" border="0" width="98%"
            style="margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;">
            <tr>
                <td align="center" valign="top">
                    <!-- [ header starts here] -->
                    <table cellspacing="0" cellpadding="0" border="0" width="650">
                        <tr>
                            <td align="center" valign="top"></td>
                        </tr>
                    </table>
                    <!-- [ middle starts here] -->
                    <table cellspacing="0" cellpadding="0" border="0" width="650">
                        <tr>
                            <td valign="top">
                                <p>
                                    <strong>Hello
                                        @if($customer)
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                        @else
                                        Guest
                                        @endif
                                    </strong>,<br />
                                    Thank you for your order from Plantshed. If you have any questions about your order
                                    please contact us at
                                    <a href="mailto:cs@plantshed.com" style="color:rgb(30,126,200)"
                                        target="_blank">cs@plantshed.com</a> or call us at
                                    <a href="tel:(212)%20662-4400" value="+12126624400"
                                        target="_blank">1-212-662-4400</a>
                                    Monday - Friday, 9am - 7pm PST and Sunday 9am - 5pm PST.
                                </p>
                                <p>Your order confirmation is below. Thank you again for your business.</p>

                                <h3 style="border-bottom:2px solid #eee; font-size:1.05em; padding-bottom:1px; ">Your
                                    Order
                                    #{{ $trans }} <small>(placed on {{ $date }})</small></h3>
                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th align="left" width="48.5%" bgcolor="#d9e5ee"
                                                style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">
                                                Billing Information:</th>
                                            <th width="3%"></th>
                                            <th align="left" width="48.5%" bgcolor="#d9e5ee"
                                                style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">
                                                Payment Method:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                @if($customer)
                                                <p>{{$customer->first_name}}{{$customer->last_name}}
                                                </p>
                                                <p>{{$customer->phone}}</p>
                                                <p>E: {{$customer->email}}</p>
                                                @if($shipping_address)
                                                <p>T: <a href="tel:+{{$shipping_address['phone']}}">{{$shipping_address['phone']}}</a></p>
                                                @endif
                                                <p>{{$customer->company_name}}</p>
                                                @else
                                                <p>Guest</p>
                                                @endif
                                            </td>
                                            <td>&nbsp;</td>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                @foreach($payments as $payment)
                                                @if($payment['status'] === 'refunded')
                                                <h4>{{ $payment['payment_type']['name']}}</h4>
                                                Type : Refunded {{$payment['payment_type']['type']}}
                                                <br />
                                                Amount: ${{$payment['amount']}}+
                                                @if($payment['payment_type']['type'] === 'card')
                                                <br />Cardholder's Name:{{ $payment['elavon_api_payments'][0]['card_holder'] }}
                                                <br />Credit Cd No:{{ $payment['elavon_api_payments'][0]['card_number'] }}
                                                <br />Credit Card Tot:{{ $payment['amount'] }}
                                                @endif
                                                @else
                                                <h4>{{ $payment['payment_type']['name']}}</h4>
                                                Type : {{$payment['payment_type']['type']}}
                                                <br />
                                                Amount: ${{$payment['amount']}}-
                                                @if($payment['payment_type']['type'] === 'card')
                                                <br />Cardholder's Name:{{ $payment['elavon_api_payments'][0]['card_holder'] }}
                                                <br />Credit Cd No:{{ $payment['elavon_api_payments'][0]['card_number'] }}
                                                <br />Credit Card Tot:{{ $payment['amount'] }}
                                                @endif
                                                @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br />
                                @if($shipping_address)
                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th align="left" width="48.5%" bgcolor="#d9e5ee"
                                                style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">
                                                Shipping Information:</th>
                                            <th width="3%"></th>
                                            <th align="left" width="48.5%" bgcolor="#d9e5ee"
                                                style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">
                                                Shipping Method:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                {{$shipping_address['first_name']}} {{ $shipping_address['last_name']}}
                                                <br />
                                                {{$shipping_address['street']}}
                                                <br />
                                                {{$shipping_address['street2']}}
                                                <br />
                                                {{$shipping_address['city']}} {{$shipping_address['region_id']['default_name']}} {{$shipping_address['postcode']}}
                                                <br />
                                                {{$shipping_address['country_id']}}
                                                <br />
                                               {{$shipping_address['company']}}
                                               <br />
                                                T: <a href="tel:+{{$shipping_address['phone']}}">{{$shipping_address['phone']}}</a></p>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                <p>Shipping type : {{ $shipping_type }}</p>
                                                <p>Shipping cost : ${{ $shipping_cost }}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br />
                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th align="left" width="48.5%" bgcolor="#d9e5ee"
                                                style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">
                                                Delivery Information:</th>
                                            <th width="3%"></th>
                                            <th align="left" width="48.5%" bgcolor="#d9e5ee"
                                                style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">
                                                Delivery Comment:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                <p>Delivery date: {{$dlvr_on}}  {{$delivery_slot}}</p>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                <p> {{$notes}}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br />
                                @endif
                                <table cellspacing="0" cellpadding="0" border="0" width="650"
                                    style="border:1px solid rgb(234,234,234)">
                                    <thead>
                                        <tr>
                                            <th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px">
                                                Item
                                            </th>
                                            <th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px">
                                                Sku
                                            </th>
                                            <th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px">
                                                Qty
                                            </th>
                                            <th align="right" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px">
                                                Price
                                            </th>
                                        </tr>
                                    </thead>
                                    @foreach($items as $item )
                                    <tbody bgcolor="#F6F6F6">
                                        <tr>
                                            <td align="left" valign="top"
                                                style="padding:3px 9px;border-bottom:1px dotted rgb(204,204,204)">
                                                <strong>{{ $item['name'] }}</strong></td>
                                            <td align="left" valign="top"
                                                style="padding:3px 9px;border-bottom:1px dotted rgb(204,204,204)">
                                                {{ $item['sku'] }}</td>
                                            <td align="center" valign="top"
                                                style="padding:3px 9px;border-bottom:1px dotted rgb(204,204,204)">
                                                {{ $item['qty'] }}</td>
                                            <td align="right" valign="top" style="padding:3px 9px; border-bottom:1px dotted rgb(204,204,204)">
                                                ${{ $item['price'] }}</td>
                                        </tr>
                                        @if($item['notes'])
                                        <tr align="left" bgcolor="#F6F6F6" valign="top"
                                                style="padding:3px 9px; border-bottom:1px dotted rgb(204,204,204)">
                                            <td align="left"style="padding:3px 9px; border-bottom:1px dotted rgb(204,204,204)">
                                                <strong> Notes : </strong>{{ $item['notes'] }} </td>
                                                <td style="padding:3px 9px; border-bottom:1px dotted rgb(204,204,204)"> </td>
                                                <td style="padding:3px 9px; border-bottom:1px dotted rgb(204,204,204)"> </td>
                                                <td style="padding:3px 9px; border-bottom:1px dotted rgb(204,204,204)"> </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                    @endforeach
                                    <tbody>
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px">Subtotal</td>
                                            <td align="right" style="padding:3px 9px">${{ $subtotal }}</td>
                                        </tr>
                                        @if($shipping_cost)
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px">Shipping Cost</td>
                                            <td align="right" style="padding:3px 9px">${{ $shipping_cost }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px"><strong>Grand Total
                                                    (Excl.Tax)</strong></td>
                                            <td align="right" style="padding:3px 9px">
                                                <strong>${{ $total_without_tax }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px">
                                                <div>Tax</div>
                                            </td>
                                            <td align="right" style="padding:3px 9px">
                                                @if($customer_no_tax)
                                                $ 0
                                                @else
                                                $ {{ $sales_tax }}
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px">Discount type and
                                                amount<br></td>
                                            <td align="right" rowspan="1" style="padding:3px 9px">
                                                {{ $data['discount_type'] }} ${{ $data['discount_amount'] }}</td>
                                        </tr> --}}
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px"><strong>Grand Total
                                                    (Incl.Tax)</strong></td>
                                            <td align="right" style="padding:3px 9px">
                                                <strong>${{ $total_ant }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                 @if(!$shipping_address && $notes )
                                <br />
                                <table cellspacing="0" cellpadding="0" border="0" width="650"
                                    style="border:1px solid rgb(234,234,234)">
                                    <thead>
                                        <tr>
                                            <th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px">
                                                Notes
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <td align="left" style="padding:3px 9px">
                                            <p>{{ $notes }}</p>
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @endif
                                <br />
                                <p>Thank you again,<br /><strong>Plantshed</strong></p>
                            </td>
                        </tr>
                    </table>
                    <br />
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
