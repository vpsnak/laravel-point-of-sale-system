<?php

namespace App\Http\Controllers;

use App\Helper\Price;
use App\MasAccount;
use App\MasOrder;
use App\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MasOrderController extends Controller
{
    const LOG_PREFIX = 'MAS';

    public static function submitToMas(Order $order)
    {
        $masAccount = MasAccount::getActive();
        $order = $order->load('store');
        $store = $order->store;

        $payload = [];
        $payload['MessageType'] = '28';
        $payload['MessageText'] = "POS Order #{$order->id}";

        $payload['SenderMdNumber'] = $masAccount->direct_id;
        $payload['FulfillerMDNumber'] = $masAccount->fulfiller_md_number;
        // $payload['FulfillerMDNumber'] = 'USZZ000035';
        $payload['PriorityType'] = '1';

        $payload['OnlinePartner'] = self::parseOnlinePartner($order);
        $payload['OnlinePartner']['MarketCode'] = $store->is_phone_center ? 'SO' : 'SW';

        $payload['OrderItems'] = self::parseOrderItems($order->items);
        $payload['RecipientDetail'] = self::parseOrderRecipient($order);


        if ($payments = self::parseOrderPayments($order->payments)) {
            $payload['Payments'] = $payments;
        }

        $payload['MdseAmount'] = $order->total_without_tax;
        $payload['TaxAmount'] = $order->total_tax;
        $payload['TotalAmount'] = $order->total;

        try {
            $client = new Client();

            $response = $client->post($masAccount->endpoint, [
                'headers' => [
                    'Authorization' => $masAccount->getAuthHeader(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'connect_timeout' => 15,
                'body' => json_encode($payload)
            ]);

            $json = (string) $response->getBody()->getContents();
            self::log('Response: ' . $json);
        } catch (\Exception $e) {
            $json = (string) $e->getResponse()->getBody();
            self::log("Error: {$json}");
        }

        $response = json_decode($json);

        self::log($response);

        if (isset($response->ErrorMessage)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'status' => 'error',
                'payload' => $payload,
                'response' => $response->ErrorMessage
            ]);

            return ['errors' => $response->ErrorMessage, 'payload' => $payload];
        }

        if (!empty($response->Messages)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'mas_control_number' => $response->Messages->ControlNumber,
                'mas_message_number' => $response->Messages->MessageNumber,
                'status' => 'success',
                'payload' => $payload,
                'response' => $response->Messages
            ]);
            return ['success' => $response->Messages, 'payload' => $payload];
        } else if (!empty($response->ControlNumber)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'mas_control_number' => $response->ControlNumber,
                'mas_message_number' => $response->MessageNumber,
                'status' => 'success',
                'payload' => $payload,
                'response' => $response
            ]);
            return ['success' => $response, 'payload' => $payload];
        } else {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'status' => 'error on success',
            ]);
            return ['errors' => [$response], 'payload' => $payload];
        }
    }

    private static function parseOnlinePartner(Order $order)
    {
        if ($order->customer && $order->billing_address) {
            $onlinePartner = [
                'SoldName' => "{$order->billing_address['first_name']} {$order->billing_address['last_name']}",
                'SoldAddress' => $order->billing_address['street'],
                'SoldAddress2' => $order->billing_address['street2'],
                'SoldCity' => $order->billing_address['city'],
                'SoldState' => $order->billing_address['region']['code'], //TODO EVALUATION
                'SoldZip' => $order->billing_address['postcode'],
                'SoldPhoneHome' => $order->billing_address['phone'],
                'SoldPhoneWork' => $order->billing_address['phone'],
                'SoldPhoneMobile' => $order->billing_address['phone'],
                'SoldEmail' => $order->customer['email'],
                'SalesRep' => '',
                'ShippingVia' => '',
                'ShippingPriority' => '1',
                'AuthCode' => '',
                'SoldAttention' => '',
                'CustomerId' => $order->customer['id'],
            ];
            return $onlinePartner;
        }
    }

    private static function parseOrderRecipient(Order $order)
    {
        $shipping_notes = '';
        if (!empty($order->notes)) {
            $shipping_notes .= "Notes: {$order->notes}";
        }
        $response = [
            'ExtensionData' => null,
            'RecipientFirstName' => 'Guest',
            'RecipientLastName' => 'Customer',
            'RecipientAttention' => $shipping_notes,
            'RecipientEmail' => 'vpallis@webo2.gr',
            'RecipientAddress' => '555 Columbus Ave',
            'RecipientAddress2' => null,
            'RecipientCity' => 'Manhattan',
            'RecipientState' => 'NY',
            'RecipientCountry' => 'US',
            'RecipientZip' => '10024',
            'RecipientPhone' => '+6974526666',
            'SpecialInstructions' => $order->delivery_slot,
            'DeliveryDate' => $order->updated_at,
            'DeliveryEndDate' => $order->updated_at,
            'CardMessage' => '',
            'OccasionType' => 9,
            'ResidenceType' => 11,
        ];

        $customer = ($order->load('customer'))->customer;
        if (empty($customer)) {
            return $response;
        }

        $shipping_address = $order->delivery['address'] ?? null;
        if (empty($shipping_address)) {
            return $response;
        }
        if (!empty($shipping_address->company)) {
            $shipping_notes .= 'Company: ' . $order->delivery['address']['company'];
        }
        // Delivery Time slots will be sent in SpecialInstructions , you can also put an abbreviated version in ShippingPriority (IE> 5P-9P for 5pm to 9pm)
        $response['RecipientFirstName'] = $shipping_address['first_name'];
        $response['RecipientLastName'] = $shipping_address['last_name'];
        $response['RecipientAttention'] = $shipping_notes;
        $response['RecipientEmail'] = $customer['email'];
        $response['RecipientAddress'] = $shipping_address['street'];
        $response['RecipientAddress2'] = $shipping_address['street2'];
        $response['RecipientCity'] = $shipping_address['city'];
        $response['RecipientState'] = $shipping_address['region']['code'];
        $response['RecipientCountry'] = $shipping_address['region']['country']['iso2_code']; //TODO EVALUATION
        $response['RecipientZip'] = $shipping_address['postcode'];
        $response['RecipientPhone'] = $shipping_address['phone'];

        if (!empty($order->delivery['occasion'])) {
            $response['OccasionType'] = $order->delivery['occasion'];
        }
        if (!empty($order->location)) {
            $response['ResidenceType'] = $shipping_address['location'];
        }

        if (!empty($order->delivery['time'])) {
            $response['SpecialInstructions'] = $order->delivery['time'];
        }

        return $response;
    }

    private static function parseOrderItems($items)
    {
        $response = [];
        foreach ($items as $item) {
            $response[$item['id']] = [
                'ItemCode' => $item['sku'],
                'ItemName' => $item['name'],
                'ItemDescription' => $item['notes'] ?? '-',
                'ItemCost' => $item['price'],
                'Quantity' => $item['qty']
            ];

            if ($item['price'] > $item['final_price']) {
                $response[$item['id']]['DiscountAmount'] = Price::numberPrecision($item['price'] - $item['final_price']);
            } else {
                $response[$item['id']]['DiscountAmount'] = "0";
            }
        }
        return array_values($response);
    }

    private static function parseOrderPayments($payments)
    {
        $i = 0;
        $response = [];
        foreach ($payments as $payment) {
            if ($payment->status !== 'approved' || $payment->refunded) {
                continue;
            }

            switch ($payment->paymentType->type) {
                case 'cash':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = "7";
                    $response[$i]['BillingAccount'] = '';
                    $response[$i]['BillingExpiration'] = '';
                    $response[$i]['BillingCv2'] = '';
                    $response[$i]['PNRefToken'] = '';
                    $response[$i]['AuthCode'] = '';
                    $response[$i]['BillingZip'] = '';
                    $response[$i]['CheckNumber'] = '';
                    $response[$i]['RoutingNumber'] = '';
                    $response[$i]['CreditCardType'] = '';
                    $response[$i]['GiftCardNumber'] = '';
                    break;
                case 'card':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = "1";
                    $response[$i]['BillingAccount'] = '';
                    $response[$i]['BillingExpiration'] = '';
                    $response[$i]['BillingCv2'] = '';
                    $response[$i]['PNRefToken'] = '';
                    $response[$i]['AuthCode'] = '';
                    $response[$i]['BillingZip'] = '';
                    $response[$i]['CheckNumber'] = '';
                    $response[$i]['RoutingNumber'] = '';
                    $response[$i]['CreditCardType'] = '';
                    $response[$i]['GiftCardNumber'] = '';
                    break;
                case 'pos-terminal':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = "1";
                    $response[$i]['BillingAccount'] = '';
                    $response[$i]['BillingExpiration'] = '';
                    $response[$i]['BillingCv2'] = '';
                    $response[$i]['PNRefToken'] = '';
                    $response[$i]['AuthCode'] = '';
                    $response[$i]['BillingZip'] = '';
                    $response[$i]['CheckNumber'] = '';
                    $response[$i]['RoutingNumber'] = '';
                    $response[$i]['CreditCardType'] = '';
                    $response[$i]['GiftCardNumber'] = '';
                    break;
                case 'house-account':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['BillingAccount'] = '';
                    $response[$i]['BillingExpiration'] = '';
                    $response[$i]['BillingCv2'] = '';
                    $response[$i]['PaymentType'] = '';
                    $response[$i]['PNRefToken'] = '';
                    $response[$i]['AuthCode'] = '';
                    $response[$i]['BillingZip'] = '';
                    $response[$i]['CheckNumber'] = '';
                    $response[$i]['RoutingNumber'] = '';
                    $response[$i]['CreditCardType'] = '';
                    $response[$i]['GiftCardNumber'] = '';
                    break;
                case 'giftcard':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = "2";
                    $response[$i]['GiftCardNumber'] = $payment->code;
                    $response[$i]['BillingAccount'] = '';
                    $response[$i]['BillingExpiration'] = '';
                    $response[$i]['BillingCv2'] = '';
                    $response[$i]['PNRefToken'] = '';
                    $response[$i]['AuthCode'] = '';
                    $response[$i]['BillingZip'] = '';
                    $response[$i]['CheckNumber'] = '';
                    $response[$i]['RoutingNumber'] = '';
                    $response[$i]['CreditCardType'] = '';
                    break;
                default:
                    break;
            }
            $i++;
        }
        return array_values($response);
    }

    public static function getOrderDetails(Order $model)
    {
        $masOrder = MasOrder::where('order_id', $model->order_id)->first();

        $masAccount = MasAccount::getActive();

        try {
            $client = new Client();

            $response = $client->get("$masAccount->endpoint/{$masOrder->mas_message_number}", [
                'headers' => [
                    'Authorization' => $masAccount->getAuthHeader(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'connect_timeout' => 15
            ]);

            $json = (string) $response->getBody()->getContents();
            self::log('Response: ' . $json);
        } catch (\Exception $e) {
            $json = (string) $e->getResponse()->getBody();
            self::log("Error: {$json}");
        }
    }

    private function updateOrderStatus()
    {
    }

    private static function log($message)
    {
        if (!is_string($message)) {
            $message = json_encode($message);
        }

        Log::channel('connector')->info(self::LOG_PREFIX . ': ' . $message);
    }
}
