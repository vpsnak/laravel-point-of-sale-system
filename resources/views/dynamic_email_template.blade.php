<div style="font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;">
<table cellspacing="0" cellpadding="0" border="0" width="98%" style="margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;">
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
                        <strong>Hello {{ $data['customer']['first_name'] }} {{ $data['customer']['last_name'] }}</strong>,<br/>
                        Thank you for your order from Plantshed.
                        If you have any questions about your order please contact us. Monday - Friday, 9am - 7pm PST and Sunday 9am - 5pm PST.
                    </p>
                    <p>Your order confirmation is below. Thank you again for your business.</p>

                    <h3 style="border-bottom:2px solid #eee; font-size:1.05em; padding-bottom:1px; ">Your Order #{{ $data['id'] }} <small>(placed on {{ $data['created_at'] }})</small></h3>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <thead>
                        <tr>
                            <th align="left" width="48.5%" bgcolor="#d9e5ee" style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">Billing Information:</th>
                            <th width="3%"></th>
                            <th align="left" width="48.5%" bgcolor="#d9e5ee" style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">Payment Method:</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td valign="top" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                <p>{{$data['customer']['first_name']}}{{$data['customer']['last_name']}}</p>
                                <p>{{$data['customer']['phone']}}</p>
                                <p>{{$data['customer']['company_name']}}</p>
                            </td>
                            <td>&nbsp;</td>
                            <td valign="top" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                                <p>{{$data['customer']['first_name']}}{{$data['customer']['last_name']}}</p>
                                <p>{{$data['customer']['phone']}}</p>
                                <p>{{$data['customer']['company_name']}}</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                     <table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <thead>
                        <tr>
                            <th align="left" width="48.5%" bgcolor="#d9e5ee" style="padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;">Order:</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td valign="top" style="padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;">
                               <p>Name : {{ $data['id'] }}</p>
                               <p>Status : {{ $data['status'] }}.</p>
                               <p>Discount type : {{ $data['discount_type'] }}.</p>
                               <p>Discount amount : {{ $data['discount_amount'] }}.</p>
                               <p>Shipping type : {{ $data['shipping_type'] }}.</p>
                               <p>Shipping cost : {{ $data['shipping_cost'] }}.</p>
                               <p>Tax : {{ $data['tax'] }}.</p>
                               <p>Subtotal : {{ $data['subtotal'] }}.</p>
                               <h1>Notes : {{ $data['notes'] }}.</h1> 
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
</div>