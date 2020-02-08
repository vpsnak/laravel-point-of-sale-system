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
        $payload['SenderMdNumber'] = $masAccount->direct_id;
        $payload['FulfillerMDNumber'] = $masAccount->fulfiller_md_number;
        // $payload['FulfillerMDNumber'] = 'USZZ000035';
        $payload['PriorityType'] = 1;
        $payload['MessageType'] = 0;
        $payload['MessageText'] = $order->id;

        $payload['RecipientDetail'] = self::parseOrderRecipient($order);

        $payload['OrderItems'] = self::parseOrderItems($order->items);

        if ($payments = self::parseOrderPayments($order->payments)) {
            $payload['Payments'] = $payments;
        }

        $payload['MdseAmount'] = round($order->total_without_tax, 2);
        $payload['TaxAmount'] = round($order->total - $order->total_without_tax, 2);
        $payload['TotalAmount'] = $order->total;

        self::log('Payload: ' . json_encode($payload));
        try {
            $client = new Client();

            $response = $client->post($masAccount->endpoint, [
                'headers' => [
                    'Authorization' => $masAccount->getAuthHeader(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'connect_timeout' => 30,
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
            foreach ($response->ErrorMessage as $error) {
                $errors[] = $error->MessageNumber . ' - ' . $error->Message;
            }
            return ['errors' => $errors];
        }

        if (!empty($response->Messages)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'mas_id' => $response->Messages->ControlNumber,
                'status' => 'success',
            ]);
            return ['success' => $response->Messages->MessageNumber . ' - ' . $response->Messages->Message];
        } else if (!empty($response->ControlNumber)) {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'mas_id' => $response->ControlNumber,
                'status' => 'success',
            ]);
            return ['success' => $response->MessageNumber . ' - ' . $response->Message];
        } else {
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'status' => 'error on success',
            ]);
            return ['error' => $response . ' - ' . $response];
        }
    }

    private static function parseOnlinePartner(Order $order)
    {

        // if ($customer = $order->customer && $billingAddress = $order->billing_address()) {
        //     $address_region = [];

        //     $onlinePartner = [
        //         'SoldName' => "{$customer->name} {$customer->name}",
        //         'SoldAddress' => $billingAddress->street,
        //         'SoldAddress2' => $billingAddress->street2,
        //         'SoldCity' => $billingAddress->city,
        //         'SoldState' => $billingAddress->,
        //         'SoldZip' => $billingAddress->postcode,
        //         'SoldPhoneHome' => $billingAddress->phone,
        //         'SoldPhoneWork' => $billingAddress->,
        //         'SoldPhoneMobile' => $billingAddress->,
        //         'SoldEmail' => $customer->email,
        //         // 'SalesRep' => $customer->,
        //         // 'ShippingVia' => $customer->,
        //         // 'ShippingPriority' => $customer->,
        //         // 'AuthCode' => $customer->,
        //         // 'SoldAttention' => $customer->,
        //         // 'CustomerId' => $customer->,
        //     ];
        //     return $onlinePartner;
        // }
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
        $response['RecipientFirstName'] = $shipping_address->first_name;
        $response['RecipientLastName'] = $shipping_address->last_name;
        $response['RecipientAttention'] = $shipping_notes;
        $response['RecipientEmail'] = $customer->email;
        $response['RecipientAddress'] = $shipping_address->street;
        $response['RecipientAddress2'] = $shipping_address->street2;
        $response['RecipientCity'] = $shipping_address->city;
        $response['RecipientState'] = $shipping_address->region_id->default_name;
        $response['RecipientCountry'] = $shipping_address->country_id;
        $response['RecipientZip'] = $shipping_address->postcode;
        $response['RecipientPhone'] = $shipping_address->phone;

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
        $response = [];
        foreach ($payments as $payment) {
            if ($payment->status !== 'approved' || $payment->refunded) {
                continue;
            }

            switch ($payment->paymentType->type) {
                case 'cash':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = "7";
                    break;
                case 'card':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = "1";
                    break;
                case 'pos-terminal':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = "1";
                    break;
                case 'house-account':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = "3";
                    break;
                case 'giftcard':
                    $response[$payment->id]['PaymentAmount'] = $payment->amount;
                    $response[$payment->id]['PaymentType'] = "2";
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
