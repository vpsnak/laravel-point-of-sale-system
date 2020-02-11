<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\MasAccount;
use App\MasOrder;
use App\Order;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MasOrderController extends Controller
{
    const LOG_PREFIX = 'MAS';

    public static function submitToMas(Order $order)
    {
        $masAccount = MasAccount::getActive();

        $payload = [];
        $payload['MessageType'] = '28';
        // $payload['MessageText'] = "POS Order ID: {$order->id}";
        $payload['MessageText'] = "POS " . now();

        $payload['SenderMdNumber'] = $masAccount->direct_id;
        $payload['FulfillerMDNumber'] = $masAccount->fulfiller_md_number;
        // $payload['FulfillerMDNumber'] = 'USZZ000035';
        $payload['PriorityType'] = '1';



        $payload['OnlinePartner'] = self::parseOnlinePartner($order);
        $payload['OrderItems'] = self::parseOrderItems($order->items);
        $payload['RecipientDetail'] = self::parseOrderRecipient($order);


        if ($payments = self::parseOrderPayments($order->payments)) {
            $payload['Payments'] = $payments;
        }

        $payload['MdseAmount'] = number_format($order->total_without_tax, 2, '.', '');
        $payload['TaxAmount'] = number_format($order->total - $order->total_without_tax, 2, '.', '');
        $payload['TotalAmount'] = number_format($order->total, 2, '.', '');

        self::log('Payload: ' . json_encode($payload));
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
        } catch (Exception $e) {
            $json = (string) $e->getResponse()->getBody();
            self::log('Error: ' . $json);
        }

        $response = json_decode($json);

        if (isset($response->ErrorMessage)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'status' => 'error',
            ]);
            $errors = [];

            return ['errors' => $response->ErrorMessage, 'payload' => $payload];
        }

        // if (isset($response->ErrorMessage)) {
        //     MasOrder::updateOrCreate(['order_id' => $order->id], [
        //         'status' => 'error',
        //     ]);
        //     $errors = [];
        //     foreach ($response->ErrorMessage as $error) {
        //         $errors[] = $error->MessageNumber . ' - ' . $error->Message;
        //     }
        //     return ['errors' => $errors];
        // }

        if (!empty($response->Messages)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'mas_id' => $response->Messages->ControlNumber,
                'status' => 'success',
            ]);
            return ['success' => $response->Messages, 'payload' => $payload];
        } else if (!empty($response->ControlNumber)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'mas_id' => $response->ControlNumber,
                'status' => 'success',
            ]);
            return ['success' => $response, 'payload' => $payload];
        } else {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'status' => 'error on success',
            ]);
            return ['error' => $response . ' - ' . $response, 'payload' => $payload];
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
                'SoldState' => $order->billing_address['state'] ?? 'NJ', // temporary hack
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
            'RecipientAddress2' => '',
            'RecipientCity' => 'Manhattan',
            'RecipientState' => 'NY',
            'RecipientCountry' => 'US',
            'RecipientZip' => '10024',
            'RecipientPhone' => '+6974526666',
            'SpecialInstructions' => $order->delivery_slot,
            'DeliveryDate' => $order->delivery_date ?? $order->updated_at,
            'DeliveryEndDate' => $order->delivery_date ?? $order->updated_at,
            'CardMessage' => '',
            'OccasionType' => 9,
            'ResidenceType' => 11,
        ];

        $customer = $order->customer;
        if (empty($customer)) {
            return $response;
        }

        $shipping_address = $order->shipping_address;
        if (empty($shipping_address)) {
            return $response;
        }
        if (!empty($shipping_address->company)) {
            $shipping_notes .= 'Company: ' . $shipping_address->company;
        }
        // Delivery Time slots will be sent in SpecialInstructions , you can also put an abbreviated version in ShippingPriority (IE> 5P-9P for 5pm to 9pm)
        $response['RecipientFirstName'] = $shipping_address['first_name'];
        $response['RecipientLastName'] = $shipping_address['last_name'];
        $response['RecipientAttention'] = $shipping_notes;
        $response['RecipientEmail'] = $customer['email'];
        $response['RecipientAddress'] = $shipping_address['street'];
        $response['RecipientAddress2'] = $shipping_address['street2'];
        $response['RecipientCity'] = $shipping_address['city'];
        $response['RecipientState'] = $shipping_address['region'] ?? null;
        $response['RecipientCountry'] = $shipping_address['country'] ?? $shipping_address['country_id'];
        $response['RecipientZip'] = $shipping_address['postcode'];
        $response['RecipientPhone'] = $shipping_address['phone'];

        if (!empty($order->occasion)) {
            $response['OccasionType'] = $order->occasion;
        }
        if (!empty($order->location)) {
            $response['ResidenceType'] = $order->location;
        }

        if (!empty($order->delivery_slot)) {
            $response['SpecialInstructions'] = $order->delivery_slot;
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
                $response[$item['id']]['DiscountAmount'] = number_format($item['price'] - $item['final_price'], 2, '.', '');
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

    private static function log($message)
    {
        Log::channel('connector')->info(self::LOG_PREFIX . ': ' . $message);
    }
}
