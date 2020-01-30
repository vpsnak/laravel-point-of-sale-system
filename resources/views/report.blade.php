<!DOCTYPE html>
<html lang="en">
​
<head>
    <meta charset="UTF-8">
    <title>Report</title>
</head>
<style>
    body {
        width: 80mm;
        height: 297mm;
    }
    table{
      font-size:12px; 
    }
    th {
      font-weight: normal;
      text-transform: uppercase;
      text-align: left;
      padding-top:2px;
    }
    td {
      text-align: right;
    }
    .title{
      text-transform:uppercase; 
      text-align: center;
    }
    .subtitle{
      text-align: center; 
      font-size:13px;
    }
</style>

@php
$report_name = $report->report_name;
$report_name_splitted = explode(" ", $report_name);
@endphp
{{-- {{$report}} --}}
    <body>
        <h2 class="title"><b>Report {{$report->report_type}}</b></h2>
            <p class="subtitle">Plantshed</p>
        <table>
          <tbody>
            <tr>
            <th>
                Report Date
            </th>
            <td>
            {{$report_name_splitted[2]}}
            </td>
            </tr>
            <tr>
            <th>
                Report Time
            </th>
            <td>
            {{$report_name_splitted[3]}}
            </td>
            </tr>
            <tr>
             <th>
                Register #
            </th>
            <td>
            {{$report->cash_register_id}}
            </td>
            </tr>
            <tr>
             <th>
               Opening Amount
            </th>
            <td>
             ${{$report->opening_amount}}
            </td>
            </tr>
            @if($report->report_type === 'z')
             <tr>
             <th>
               Closing Amount
            </th>
            <td>
             ${{$report->closing_amount}}
            </td>
            </tr>
            @endif
            <tr>
             <th>
               Subtotal
            </th>
            <td>
             ${{$report->subtotal}}
            </td>
            </tr>
              <tr>
             <th>
               Tax
            </th>
            <td>
             ${{$report->tax}}
            </td>
            </tr>
             </tr>
              <tr>
             <th>
               Total
            </th>
            <td>
             ${{$report->total}}
            </td>
            </tr>
            <tr>
             <th>
               Cash Total
            </th>
            <td>
             ${{$report->cash_total}}
            </td>
            </tr>
            <tr>
             <th>
               Gift Card Total
            </th>
            <td>
             ${{$report->gift_card_total}}
            </td>
            </tr>
            <tr>
             <th>
               Credit Card Total
            </th>
            <td>
             ${{$report->credit_card_total}}
            </td>
            </tr>
             <tr>
             <th>
               Pos Terminal Total
            </th>
            <td>
             ${{$report->pos_terminal_total}}
            </td>
            </tr>
            </tr>
             <tr>
             <th>
               Change Total
            </th>
            <td>
             ${{$report->change_total}}
            </td>
            </tr>
            <tr>
            <th>
             Cash Tax
            </th>
            <td>
             ${{$report->cash_tax}}
            </td>
            </tr>
            <tr>
            <th>
             Gift Card Tax
            </th>
            <td>
             ${{$report->gift_card_tax}}
            </td>
            </tr>
             <tr>
            <th>
             Credit Card Tax
            </th>
            <td>
             ${{$report->credit_card_tax}}
            </tr>
             <tr>
            <th>
             Pos Terminal Tax
            </th>
            <td>
             ${{$report->pos_terminal_tax}}
            </tr>
             <tr>
            <th>
             Order Count
            </th>
            <td>
             {{$report->order_count}}
            </tr>
            <tr>
            <th>
             Order Product Count
            </th>
            <td>
             {{$report->order_product_count}}
            </tr>
            <tr>
            <th>
             Order Refund Count
            </th>
            <td>
             {{$report->order_refund_count}}
            </tr>
             <tr>
            <th>
             Order Refund Total
            </th>
            <td>
             ${{$report->order_refund_total}}
            </tr>
              <tr>
            <th>
             Order Complete Count
            </th>
            <td>
             {{$report->order_complete_count}}
            </tr>
              <tr>
            <th>
             Order Complete Total
            </th>
            <td>
             ${{$report->order_complete_total}}
            </tr>
             <tr>
            <th>
             Order Retail Count
            </th>
            <td>
             {{$report->order_retail_count}}
            </tr>
            <tr>
            <th>
             Order In Store Pick up count
            </th>
            <td>
             {{$report->order_in_store_count}}
            </tr>
             <tr>
            <th>
             Order Delivery count
            </th>
            <td>
             {{$report->order_in_store_count}}
            </tr>
            <tbody>
        </table>
        </div>
    </body>
</html>

