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
                                    <strong>Hello {{ $data['customer']['first_name'] }}
                                        {{ $data['customer']['last_name'] }}</strong>,<br />
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
                                    #{{ $data['id'] }} <small>(placed on {{ $data['created_at'] }})</small></h3>
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
                                                @if($data['customer'])
                                                <p>{{$data['customer']['first_name']}}{{$data['customer']['last_name']}}
                                                </p>
                                                <p>{{$data['customer']['phone']}}</p>
                                                <p>E: {{$data['customer']['email']}}</p>
                                                <p>T: <a href="tel:+{{$data['shipping_address']['phone']}}">{{$data['shipping_address']['phone']}}</a></p>
                                                <p>{{$data['customer']['company_name']}}</p>
                                                @else
                                                <p>Guest</p>  
                                                @endif
                                            </td>
                                            <td>&nbsp;</td>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                @foreach(json_decode($data['payments'] , true) as $payment)
                                                <h4>{{ $payment['payment_type']['name']}}</h4>
                                                Type : {{$payment['payment_type']['type']}}
                                                <br />
                                                Amount: ${{$payment['amount']}}
                                                @if($payment['code'])
                                                <br />
                                                Code: {{$payment['code']}}
                                                @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br />
                                @if($data['shipping_address'])
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
                                                {{$data['shipping_address']['first_name']}} {{$data['shipping_address']['last_name']}}
                                                <br />
                                                {{$data['shipping_address']['street']}}
                                                <br />
                                                {{$data['shipping_address']['street2']}}
                                                <br />
                                                {{$data['shipping_address']['city']}} {{$data['shipping_address']['region_id']['default_name']}} {{$data['shipping_address']['postcode']}}
                                                <br />
                                                {{$data['shipping_address']['country_id']}}
                                                <br />
                                               {{$data['shipping_address']['company']}}
                                               <br />
                                                T: <a href="tel:+{{$data['shipping_address']['phone']}}">{{$data['shipping_address']['phone']}}</a></p>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                <p>Shipping type : {{ $data['shipping_type'] }}.</p>
                                                <p>Shipping cost : ${{ $data['shipping_cost'] }}.</p>
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
                                                <p>Delivery date: {{$data['delivery_date']}}</p>
                                                <p>Occasion: {{$data['occasion']}}</p>
                                                <p>Location: {{$data['location']}}</p>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td valign="top"
                                                style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                                <p> {{$data['notes']}}</p>
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
                                    @foreach($data['items'] as $item )
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
                                            <td align="right" style="padding:3px 9px">${{ $data['subtotal'] }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px">Shipping Cost</td>
                                            <td align="right" style="padding:3px 9px">${{ $data['shipping_cost'] }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px"><strong>Grand Total
                                                    (Excl.Tax)</strong></td>
                                            <td align="right" style="padding:3px 9px">
                                                <strong>${{ $data['subtotal'] }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right" style="padding:3px 9px">
                                                <div>Tax</div>
                                            </td>
                                            <td align="right" style="padding:3px 9px">
                                                @if(!$data['customer']['no_tax'])
                                                $ {{ $data['total'] - $data['total_without_tax'] }} </td>
                                                @else
                                                $ 0
                                                @endif
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
                                                <strong>${{ $data['total'] }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                 @if(!$data['shipping_address'])
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
                                            <p>{{ $data['notes'] }}</p>
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