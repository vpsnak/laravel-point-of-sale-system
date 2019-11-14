<?php

namespace App\Http\Controllers;

use App\MasOrder;
use App\Order;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MasOrderController extends Controller
{
    const LOG_PREFIX = 'MAS';
    protected static $mas_direct_id = 'USZZ000035';
    protected static $user = 'pshed';
    protected static $pass = '9JNH76k#';
    protected static $new_order_endpoint = 'http://masapitest.cloudapp.net/MASDirect.Api.Service.svc/api/messagesj';

    protected $model = MasOrder::class;

    public function submitToMas(Order $order)
    {
        $payload = [];
        $payload['SenderMdNumber'] = self::$mas_direct_id;
        $payload['FulfillerMDNumber'] = 'USNY000012';
//        $payload['FulfillerMDNumber'] = 'USZZ000035';
        $payload['PriorityType'] = 1;
        $payload['MessageType'] = 0;
        $payload['MessageText'] = $order->id;

        // Delivery Time slots will be sent in SpecialInstructions , you can also put an abbreviated version in ShippingPriority (IE> 5P-9P for 5pm to 9pm)
        $shipping_address = $order->shipping_address;
        if (empty($shipping_address)) {
            return;
        }
        $shipping_notes = '';
        if (!empty($shipping_address->company)) {
            $shipping_notes .= 'Company: ' . $shipping_address->company;
        }
        if (!empty($order->notes)) {
            $shipping_notes .= 'Notes: ' . $order->notes;
        }
        $payload['RecipientDetail'] = [
            'ExtensionData' => null,
            'RecipientFirstName' => $shipping_address->first_name,
            'RecipientLastName' => $shipping_address->last_name,
            'RecipientAttention' => $shipping_notes,
            'RecipientEmail' => $shipping_address->first_name,
            'RecipientAddress' => $shipping_address->street,
            'RecipientAddress2' => $shipping_address->street2,
            'RecipientCity' => $shipping_address->city,
            'RecipientState' => $shipping_address->region_id->default_name,
            'RecipientCountry' => $shipping_address->country_id,
            'RecipientZip' => $shipping_address->postcode,
            'RecipientPhone' => $shipping_address->phone,
            'SpecialInstructions' => $order->delivery_slot,
            'DeliveryDate' => $order->delivery_date,
            'DeliveryEndDate' => $order->delivery_date,
            'CardMessage' => '',
            'OccasionType' => $order->occasion,
            'ResidenceType' => $order->location,
        ];

        $payload['OrderItems'] = $this->parseOrderItems($order->items);

        if ($payments = $this->parseOrderPayments($order->payments)) {
            $payload['Payments'] = $payments;
        }

        $payload['MdseAmount'] = $order->total_without_tax;
        $payload['TaxAmount'] = $order->total - $order->total_without_tax;
        $payload['TotalAmount'] = $order->total;

        self::log('Payload: ' . json_encode($payload));
        try {
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

            self::log('Response: ' . (string)$response->getBody()->getContents());
            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            self::log('Error: ' . (string)$e->getResponse()->getBody());
            return;
        }
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

    private static function log($message)
    {
        Log::channel('connector')->info(self::LOG_PREFIX . ': ' . $message);
    }
}
