<?php

namespace App\Http\Controllers;

use App\Helper\Price;
use App\MasAccount;
use App\MasOrder;
use App\MasOrderLog;
use App\Order;
use GuzzleHttp\Client;

class MasOrderController extends Controller
{
    private $masAccount;
    private $order;
    private $store;


    public function __construct()
    {
        $this->masAccount = MasAccount::getActive();
    }

    public function submitToMas(Order $order)
    {
        $this->order = $order->load('store');
        $this->store = $order->store;

        $payload = [];
        $payload['MessageType'] = '28';
        $payload['MessageText'] = "POS Order #{$this->order->id}";

        $payload['SenderMdNumber'] = $this->masAccount->direct_id;
        $payload['FulfillerMDNumber'] = $this->masAccount->fulfiller_md_number;
        // $payload['FulfillerMDNumber'] = 'USZZ000035';
        $payload['PriorityType'] = '1';

        $payload['OnlinePartner'] = $this->parseOnlinePartner();
        $payload['OnlinePartner']['MarketCode'] = $this->store->is_phone_center ? 'SO' : 'SW';

        $payload['OrderItems'] = $this->parseOrderItems();
        $payload['RecipientDetail'] = $this->parseOrderRecipient();


        if ($payments = $this->parseOrderPayments()) {
            $payload['Payments'] = $payments;
        }

        $payload['MdseAmount'] = $this->order->total_without_tax;
        $payload['TaxAmount'] = $this->order->total_tax;
        $payload['TotalAmount'] = $this->order->total;

        try {
            $client = new Client();

            if ($this->masAccount->environment === 'production') {
                $url = 'messages';
            } else {
                $url = 'messagesj';
            }

            $response = $client->post("{$this->masAccount->endpoint}/{$url}", [
                'headers' => [
                    'Authorization' => $this->masAccount->getAuthHeader(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'connect_timeout' => 15,
                'body' => json_encode($payload)
            ]);

            $response = (string) $response->getBody()->getContents();
            $status = 'success';
        } catch (Exception $e) {
            $response = (string) $e->getResponse()->getBody();
            $status = 'error';
        }

        $response = json_decode($response);

        self::log($payload, $response, $status);

        if (isset($response->ErrorMessage)) {
<<<<<<< HEAD
            $masOrder = MasOrder::updateOrCreate(['order_id' => $this->order->id], [
                'status' => 'error'
=======
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'status' => 'error',
                'payload' => $payload,
                'response' => $response->ErrorMessage,
                'env' => $masAccount->environment
>>>>>>> e1e900cdb973521faf532dc0863ec55b9b8fd604
            ]);

            return ['errors' => $response->ErrorMessage, 'payload' => $payload];
        }

        if (!empty($response->Messages)) {
            MasOrder::updateOrCreate(['order_id' => $this->order->id], [
                'mas_control_number' => $response->Messages->ControlNumber,
                'mas_message_number' => $response->Messages->MessageNumber,
                'status' => 'submitted',
<<<<<<< HEAD
=======
                'payload' => $payload,
                'response' => $response->Messages,
                'env' => $masAccount->environment
>>>>>>> e1e900cdb973521faf532dc0863ec55b9b8fd604
            ]);
            return ['success' => $response->Messages, 'payload' => $payload];
        } else if (!empty($response->ControlNumber)) {
            MasOrder::updateOrCreate(['order_id' => $this->order->id], [
                'mas_control_number' => $response->ControlNumber,
                'mas_message_number' => $response->MessageNumber,
                'status' => 'submitted',
                'payload' => $payload,
                'response' => $response,
                'env' => $masAccount->environment
            ]);
            return ['success' => $response, 'payload' => $payload];
        } else {
<<<<<<< HEAD
            MasOrder::updateOrCreate(['order_id' => $this->order->id], [
                'status' => 'error on success',
=======
            MasOrder::updateOrCreate(['order_id' => $order->id], [
                'status' => 'error',
                'payload' => $payload,
                'response' => $response,
                'env' => $masAccount->environment
>>>>>>> e1e900cdb973521faf532dc0863ec55b9b8fd604
            ]);
            return ['errors' => [$response], 'payload' => $payload];
        }
    }

    private function parseOnlinePartner()
    {
        if ($this->order->customer && $this->order->billing_address) {
            $onlinePartner = [
                'SoldName' => "{$this->order->billing_address['first_name']} {$this->order->billing_address['last_name']}",
                'SoldAddress' => $this->order->billing_address['street'],
                'SoldAddress2' => $this->order->billing_address['street2'],
                'SoldCity' => $this->order->billing_address['city'],
                'SoldState' => $this->order->billing_address['region']['code'], //TODO EVALUATION
                'SoldZip' => $this->order->billing_address['postcode'],
                'SoldPhoneHome' => $this->order->billing_address['phone'],
                'SoldPhoneWork' => $this->order->billing_address['phone'],
                'SoldPhoneMobile' => $this->order->billing_address['phone'],
                'SoldEmail' => $this->order->customer['email'],
                'SalesRep' => '',
                'ShippingVia' => '',
                'ShippingPriority' => '1',
                'AuthCode' => '',
                'SoldAttention' => '',
                'CustomerId' => $this->order->customer['id'],
            ];
            return $onlinePartner;
        }
    }

    private function parseOrderRecipient()
    {
        $shipping_notes = '';
        if (!empty($this->order->notes)) {
            $shipping_notes .= "Notes: {$this->order->notes}";
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
            'SpecialInstructions' => $this->order->delivery_slot,
            'DeliveryDate' => $this->order->updated_at,
            'DeliveryEndDate' => $this->order->updated_at,
            'CardMessage' => '',
            'OccasionType' => 9,
            'ResidenceType' => 11,
        ];

        $customer = ($this->order->load('customer'))->customer;
        if (empty($customer)) {
            return $response;
        }

        $shipping_address = $this->order->delivery['address'] ?? null;
        if (empty($shipping_address)) {
            return $response;
        }
        if (!empty($shipping_address->company)) {
            $shipping_notes .= 'Company: ' . $this->order->delivery['address']['company'];
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

        if (!empty($this->order->delivery['occasion'])) {
            $response['OccasionType'] = $this->order->delivery['occasion'];
        }
        if (!empty($this->order->location)) {
            $response['ResidenceType'] = $shipping_address['location'];
        }

        if (!empty($this->order->delivery['time'])) {
            $response['SpecialInstructions'] = $this->order->delivery['time'];
        }

        return $response;
    }

    private function parseOrderItems()
    {
        $response = [];
        foreach ($this->order->items as $item) {
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

    private function parseOrderPayments()
    {
        $i = 0;
        $response = [];
        foreach ($this->order->payments as $payment) {
            if ($payment->status !== 'approved' || $payment->refunded) {
                continue;
            }

            switch ($payment->paymentType->type) {
                case 'cash':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = 7;
                    $response[$i]['AuthCode'] = 'cash';
                    break;
                case 'card':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = 1;
                    $response[$i]['BillingAccount'] = '';
                    $response[$i]['BillingExpiration'] = '';
                    $response[$i]['BillingCv2'] = '';
                    $response[$i]['PNRefToken'] = '';
                    $response[$i]['AuthCode'] = $payment->code;
                    $response[$i]['BillingZip'] = '';
                    $response[$i]['CheckNumber'] = '';
                    $response[$i]['RoutingNumber'] = '';
                    $response[$i]['CreditCardType'] = '';
                    break;
                case 'pos-terminal':
                    $log = $payment->elavonSdkPayments()->latest()->first();

                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = 1;
                    $response[$i]['PNRefToken'] = null;
                    $response[$i]['AuthCode'] = $payment->code;
                    $response[$i]['BillingZip'] = '';
                    $response[$i]['CreditCardType'] = MasOrder::getCreditCardType($log->getPaymentTransactionData()['cardScheme']);
                    break;
                case 'house-account':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['BillingAccount'] = '';
                    $response[$i]['BillingExpiration'] = '';
                    $response[$i]['BillingCv2'] = '';
                    $response[$i]['PaymentType'] = '';
                    $response[$i]['PNRefToken'] = null;
                    $response[$i]['AuthCode'] = 'house_account';
                    break;
                case 'giftcard':
                    $response[$i]['PaymentAmount'] = $payment->amount;
                    $response[$i]['PaymentType'] = 2;
                    $response[$i]['GiftCardNumber'] = $payment->code;
                    $response[$i]['PNRefToken'] = null;
                    $response[$i]['AuthCode'] = 'giftcard';
                    break;
                default:
                    break;
            }
            $i++;
        }
        return array_values($response);
    }

    public function getOrderDetails(Order $model)
    {
        $this->order = $model;

        if ($this->order->masOrder) {
            $masAccount = MasAccount::getActive();
            $mas_message_number = $this->order->masOrder->mas_message_number;
            try {
                $client = new Client();

                $response = $client->get("$masAccount->endpoint/orders/{$this->order->masOrder->mas_message_number}", [
                    'headers' => [
                        'Authorization' => $masAccount->getAuthHeader(),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ],
                    'connect_timeout' => 15
                ]);

                $response = (string) $response->getBody()->getContents();
                $status = 'success';
                return response($response);
            } catch (Exception $e) {
                $response = (string) $e->getResponse()->getBody();
                $status = 'failed';
            }

            self::log("get Order {$mas_message_number}", $response, $status);
        } else {
            return response(['errors' => ["Order #{$this->order->id} is not submitted to MAS"]]);
        }
    }

    private function updateOrderStatus()
    {
    }

    private function log($payload, $response, $status)
    {
        if (!is_string($payload)) {
            $payload = json_encode($payload);
        }

        if (!is_string($response)) {
            $response = json_encode($response);
        }

        MasOrderLog::create([
            'mas_order_id' => $this->order->id,
            'payload' => $payload,
            'response' => $response,
            'status' => $status,
            'environment' => $this->masAccount->environment
        ]);
    }
}
