<?php

namespace App\Http\Controllers;

use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ElavonApiPaymentController extends Controller
{
    protected static $enviroment_url = 'https://api.demo.convergepay.com/VirtualMerchantDemo/processxml.do';

    protected static $merchant_id = '009710';
    protected static $user_id = 'convergeapi';
    protected static $pin = 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8';
    protected static $test_mode = 'false';

    public static function doTransaction($type, array $data)
    {
        $defaults = [
            'ssl_merchant_id' => self::$merchant_id,
            'ssl_user_id' => self::$user_id,
            'ssl_pin' => self::$pin,
            'ssl_test_mode' => self::$test_mode,
            'ssl_transaction_type' => $type,
            'ssl_show_form' => 'false' // @TODO check this attribute when and if neeed
        ];

        $payload = array_merge($defaults, $data);
        $html = self::generateHtmlPayload($payload);
        $response = self::sendTransaction($html);
        return (array)$response;
    }

    private static function generateHtmlPayload(array $data)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $new_tag = $dom->createElement('txn');
        foreach ($data as $key => $value) {
            $new_tag->appendChild($dom->createElement($key, $value));
        }
        $dom->appendChild($new_tag);
        return $dom->saveHTML();
    }

    private static function sendTransaction($html)
    {
        $client = new Client();
        Log::debug('CreditCard Payment Payload:' . json_encode($html));
        $response = $client->post(self::$enviroment_url, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => ['xmldata' => $html]
        ]);

        $responseToXml = simplexml_load_string($response->getBody());
        Log::debug('CreditCard Payment Response:' . json_encode($responseToXml));
        return $responseToXml;
    }
}
