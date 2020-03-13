<?php

namespace App\Http\Controllers;

use App\ElavonApiTransaction;
use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ElavonApiTransactionController extends Controller
{
    private $txn_id;
    private $bankAccountApi;

    private $ssl_card_number;
    private $ssl_amount;
    private $ssl_cvv2cvc2_indicator;
    private $ssl_cvv2cvc2;

    public function __construct()
    {
        $this->bankAccountApi =
            auth()->user()->open_register->cash_register->store->company->bankAccountApi()->account;
    }

    private function saveToApiLog($data, $status)
    {
        $elavonApiPayment = new ElavonApiTransaction();

        $elavonApiPayment->txn_id = $this->txn_id;
        $elavonApiPayment->status = $status;
        $elavonApiPayment->log = $data;

        $elavonApiPayment->save();
    }

    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'test_case' => 'nullable|string',
            'ssl_card_number' => 'nullable|numeric',
            'ssl_amount' => 'nullable|numeric',
            'ssl_cvv2cvc2_indicator' => 'nullable|numeric',
            'ssl_cvv2cvc2' => 'nullable|numeric',
        ]);

        $this->test_case = $validatedData['test_case'] ?? null;
        $this->ssl_card_number = $validatedData['ssl_card_number'] ?? null;
        $this->ssl_amount = $validatedData['ssl_amount'] ?? null;
        $this->ssl_cvv2cvc2_indicator = $validatedData['ssl_cvv2cvc2_indicator'] ?? null;
        $this->ssl_cvv2cvc2 = $validatedData['ssl_cvv2cvc2'] ?? null;
    }

    public function doTransaction($type, array $data)
    {
        $defaults = [
            'ssl_transaction_type' => $type,

            'ssl_merchant_id' => $this->bankAccountApi['ssl_merchant_id'],
            'ssl_user_id' => $this->bankAccountApi['ssl_user_id'],
            'ssl_pin' => $this->bankAccountApi['ssl_pin']
        ];

        $payload = array_merge($defaults, $data);
        $html = self::generateHtmlPayload($payload);
        $response = $this->sendTransaction($html);
        return (array) $response;
    }

    private static function generateHtmlPayload(array $data)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $txn = $dom->createElement('txn');
        foreach ($data as $key => $value) {
            $txn->appendChild($dom->createElement($key, $value));
        }
        $dom->appendChild($txn);

        return $dom->saveHTML();
    }

    private function sendTransaction($html)
    {
        $client = new Client();
        Log::debug('CreditCard Payment Payload:' . json_encode($html));

        $response = $client->post($this->bankAccountApi['endpoint'], [
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
