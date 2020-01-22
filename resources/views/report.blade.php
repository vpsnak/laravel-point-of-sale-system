<!DOCTYPE html>
<html lang="en">
​
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    body {
        width: 80mm;
        height: 297mm;
    }
</style>

@php
$report_name = $report->report_name;
$report_name_splitted = explode(" ", $report_name);
@endphp
{{-- {{$report}} --}}
    <body>
        <div style=" background-color: white; width:auto; height:auto; font-weight:normal; margin-bottom:20px;">
        <h2 style="text-transform:uppercase; text-align: center;"><b>Report {{$report->report_type}}</b></h2>
            <p style="text-align: center; font-size:13px;">Plantshed</p>
        <table style="font-size:12px; display:inline-table; width:100%;">
            <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal;">
                Report Date
            </th>
            <td align="right" style="text-transform:uppercase; font-weight:normal;">
            {{$report_name_splitted[2]}}
            </td>
            </tr>
            <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal;">
                Report Time
            </th>
            <td align="right" style="text-transform:uppercase;">
            {{$report_name_splitted[3]}}
            </td>
            </tr>
            <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
                Register #
            </th>
            <td align="right" style="text-transform:uppercase;">
            {{$report->cash_register_id}}
            </td>
            </tr>
            <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Opening Amount
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->opening_amount}}
            </td>
            </tr>
            @if($report->report_type === 'z')
             <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Closing Amount
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->closing_amount}}
            </td>
            </tr>
            @endif
            <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Subtotal
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->subtotal}}
            </td>
            </tr>
              <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Tax
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->tax}}
            </td>
            </tr>
             </tr>
              <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->total}}
            </td>
            </tr>
            <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Cash Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->cash_total}}
            </td>
            </tr>
            <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Gift Card Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->gift_card_total}}
            </td>
            </tr>
            <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Credit Card Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->credit_card_total}}
            </td>
            </tr>
             <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Pos Terminal Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->pos_terminal_total}}
            </td>
            </tr>
            </tr>
             <tr>
             <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
               Change Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->change_total}}
            </td>
            </tr>
            <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Cash Tax
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->cash_tax}}
            </td>
            </tr>
            <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Gift Card Tax
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->gift_card_tax}}
            </td>
            </tr>
             <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Credit Card Tax
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->credit_card_tax}}
            </tr>
             <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Pos Terminal Tax
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->pos_terminal_tax}}
            </tr>
             <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order Count
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_count}}
            </tr>
            <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order Product Count
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_product_count}}
            </tr>
            <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order Refund Count
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_refund_count}}
            </tr>
             <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order Refund Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_refund_total}}
            </tr>
              <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order Complete Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_complete_count}}
            </tr>
             <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order Retail Total
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_retail_count}}
            </tr>
            <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order In Store Pick up count
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_in_store_count}}
            </tr>
             <tr>
            <th align="left" style="text-transform:uppercase; font-weight:normal; padding-top:2px;">
             Order Delivery count
            </th>
            <td align="right" style="text-transform:uppercase;">
             ${{$report->order_in_store_count}}
            </tr>
        </table>
        </div>
    </body>
</html>

