<?php

namespace App\Http\Controllers;

use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CreditCardController extends Controller
{
    protected static $merchant_id = '009710';
    protected static $user_id = 'convergeapi';
    protected static $pin = 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8';

    protected static $enviroment_url = 'https://api.demo.convergepay.com/VirtualMerchantDemo/processxml.do';

    public static function cardPayment($card_number, $exp_date, $cvc, $card_holder, $amount)
    {
        $client = new Client();
        $xml = self::getCardPaymentPayload($card_number, $exp_date, $cvc, $card_holder, $amount);

        Log::debug('CreditCard Payment Payload:' . json_encode($xml));
        $response = $client->post(self::$enviroment_url, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => ['xmldata' => $xml]
        ]);

        $responseToXml = simplexml_load_string($response->getBody());
        Log::debug('CreditCard Payment Response:' . json_encode($responseToXml));
        return $responseToXml;
    }

    public static function getCardPaymentPayload(
        $card_number,
        $exp_date,
        $cvc,
        $card_holder,
        $amount
    ) {
        $payload = [
            'ssl_merchant_id' => self::$merchant_id,
            'ssl_user_id' => self::$user_id,
            'ssl_pin' => self::$pin,
            'ssl_test_mode' => 'true',
            'ssl_transaction_type' => 'ccsale',
            'ssl_card_number' => $card_number,
            'ssl_exp_date' => $exp_date,
            'ssl_amount' => $amount,
            'ssl_cvv2cvc2_indicator' => '1',
            'ssl_cvv2cvc2' => $cvc,
            'ssl_first_name' => $card_holder,
            'ssl_show_form' => 'false'
        ];

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $new_tag = $dom->createElement('txn');
        foreach ($payload as $key => $value) {
            $new_tag->appendChild($dom->createElement($key, $value));
        }
        $dom->appendChild($new_tag);
        return $dom->saveHTML();
    }
}

