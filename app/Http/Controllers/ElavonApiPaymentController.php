<?php

namespace App\Http\Controllers;

use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\ElavonApiPayment;

class ElavonApiPaymentController extends Controller
{
    protected static $enviroment_url = 'https://api.demo.convergepay.com/VirtualMerchantDemo/processxml.do';

    protected static $merchant_id = '009710';
    protected static $user_id = 'convergeapi';
    protected static $pin = 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8';
    protected static $test_mode = 'false';
    private $txn_id;
    private $test_case;
    private $card_number;

    public function getLogs($test_case = null)
    {
        if ($test_case) {
            return response(ElavonApiPayment::whereTestCase($test_case)->get());
        } else {
            return response(ElavonApiPayment::all());
        }
    }

    public function deleteAll()
    {
        DB::table('elavon_api_payments')->truncate();
        return response('API Logs truncated');
    }

    private function saveToApiLog($data, $status)
    {
        $elavonApiPayment = new ElavonApiPayment();

        $elavonApiPayment->test_case = $this->test_case;
        $elavonApiPayment->txn_id = $this->txn_id;
        $elavonApiPayment->status = $status;
        $elavonApiPayment->log = is_Array($data) ? json_encode($data) : strip_tags($data);

        $elavonApiPayment->save();
    }

    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'test_case' => 'nullable|string',
            'card_number' => 'nullable|numeric',
            '' => '',
            '' => '',
        ]);

        $this->test_case = isset($validatedData['test_case']) ? $validatedData['test_case'] : null;
        $this->card_number = isset($validatedData['card_number']) ? $validatedData['card_number'] : null;
    }

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
        return (array) $response;
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
