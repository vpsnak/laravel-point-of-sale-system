<?php

namespace App\Http\Controllers;

use App\MasOrder;
use App\Order;
use GuzzleHttp\Client;

class MasOrderController extends Controller
{
    protected static $mas_direct_id = 'USZZ000035';
    protected static $user = 'pshed';
    protected static $pass = '9JNH76k#';
    protected static $new_order_endpoint = 'http://masapitest.cloudapp.net/MASDirect.Api.Service.svc/api/messagesj';

    protected $model = MasOrder::class;

    public function submitToMas(Order $order)
    {
        $payload = [];
        $payload['SenderMdNumber'] = self::$mas_direct_id;
        $payload['FulfillerMDNumber'] = self::$mas_direct_id;
        $payload['PriorityType'] = 1; // @TODO ask whats that
        $payload['MessageType'] = 0;
        $payload['MessageText'] = 'test comment';

        $payload['RecipientDetail'] = [
            'ExtensionData' => null,
            'RecipientFirstName' => 'Test',
            'RecipientLastName' => 'Order',
            'RecipientAttention' => 'Test Company',
            'RecipientEmail' => 'test@testing.com',
            'RecipientAddress' => '123 Test Dr',
            'RecipientAddress2' => 'Suite 23',
            'RecipientCity' => 'Dallas',
            'RecipientState' => 'TX',
            'RecipientCountry' => 'US',
            'RecipientZip' => '80211',
            'RecipientPhone' => '2149994455',
            'RecipientLatLong' => '',
            'SpecialInstructions' => '',
            'DeliveryDate' => '02/14/2030',
            'DeliveryEndDate' => '02/14/2030',
            'CardMessage' => 'This is a message for you!',
            'OccasionType' => 2,
            'ResidenceType' => 6,
        ];

//        @TODO add order comments in recipient
//        if (!empty($order->notes)) {
//            $payload['MessageText'] = $order->notes;
//        }

        $payload['OrderItems'] = $this->parseOrderItems($order->items);

        if ($payments = $this->parseOrderPayments($order->payments)) {
            $payload['Payments'] = $payments;
        }

        $subtotal = $order->subtotal + $order->shipping_cost;
        $payload['MdseAmount'] = $subtotal;
        $payload['TaxAmount'] = $subtotal * $order->tax / 100;
        $payload['TotalAmount'] = $order->total;
        $payload['OrderId'] = $order->id;

        print_r(json_encode($payload));

        $client = new Client();
        $response = $client->post(self::$new_order_endpoint, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(self::$user . ':' . self::$pass . ':' . self::$mas_direct_id),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'connect_timeout' => 30,
            'body' => json_encode($payload)
        ]);

        return $response->getBody()->getContents();
    }

    private function parseOrderItems($items)
    {
        $response = [];
        foreach ($items as $item) {
            $response[$item->id] = [
                'ItemCode' => $item->sku,
                'ItemName' => $item->name,
                'ItemDescription' => $item->notes ?? '-',
                'ItemCost' => $item->price,
                'Quantity' => $item->qty
            ];

            if ($item->price > $item->final_price) {
                $response[$item->id]['DiscountAmount'] = number_format($item->final_price, 2);
            }
        }
        return array_values($response);
    }

    private function parseOrderPayments($payments)
    {
        $response = [];
        foreach ($payments as $payment) {
            if ($payment->status != 'approved' || $payment->refunded == 1) {
                continue;
            }

            $response[$payment->id] = [
                'PaymentAmount' => $payment->amount
            ];

            switch ($payment->paymentType->type) {
                case 'cash':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = 7;
                    break;
                case 'card':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = 1;
                    break;
                case 'pos-terminal':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = 1;
                    break;
                case 'house-account':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = 3;
                    break;
                case 'giftcard':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = 2;
                    $response[$payment->id]['GiftCardNumber'] = $payment->code;
                    break;
            }
        }
        return array_values($response);
    }
}
