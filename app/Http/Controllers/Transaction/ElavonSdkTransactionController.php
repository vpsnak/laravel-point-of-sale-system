<?php

namespace App\Http\Controllers;

use App\ElavonSdkTransaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ElavonSdkTransactionController extends Controller
{
    public $paymentGatewayId;
    public $chanId;
    public $selected_transaction;
    public $amount;
    public $originalTransId;
    public $taxFree;
    public $keyed;
    public $voiceReferral;
    public $invoiceNumber;
    public $transaction_id;
    public $CARDHOLDER_ADDRESS;
    public $CARDHOLDER_ZIP;

    public function all()
    {
        return response(ElavonSdkTransaction::paginate(10));
    }

    public function lookup(Request $request)
    {
        $validatedData = $request->validate([
            'creditCard' => 'nullable',
            'last4CC' => 'nullable|numeric|digits:4',
            'beginDate' => 'nullable',
            'endDate' => 'nullable',
            'transId' => 'nullable',
            'utcDate' => 'nullable|required_with:transId',
            'merchantTransactionReference' => 'nullable'
        ]);

        $response = $this->openPaymentGateway();

        if (array_key_exists('errors', $response)) {
            return $response;
        }

        return $this->searchPaymentTransaction($validatedData);
    }

    private function searchPaymentTransaction(array $parameters)
    {
        $payload = [
            "method" => "searchPaymentTransaction",
            "requestId" => "19183388",
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                'paymentGatewayId' => $this->paymentGatewayId
            ]
        ];
        $payload['parameters'] += $parameters;

        return $this->sendRequest($payload);
    }

    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'test_case' => 'sometimes|string',
            'selected_transaction' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
            'originalTransId' => 'nullable|string',
            'taxFree' => 'nullable|boolean',
            'keyed' => 'nullable|boolean',
            'voiceReferral' => 'nullable|boolean',
            'invoiceNumber' => 'nullable|string',
            'CARDHOLDER_ADDRESS' => 'nullable|string|max:30',
            'CARDHOLDER_ZIP' => 'nullable|string|max:9'
        ]);

        $this->selected_transaction = $validatedData['selected_transaction'];

        array_key_exists('amount', $validatedData) ? $this->amount = $validatedData['amount'] : null;
        array_key_exists('originalTransId', $validatedData) ? $this->originalTransId = $validatedData['originalTransId'] : null;
        array_key_exists('keyed', $validatedData) ? $this->keyed = $validatedData['keyed'] : null;
        array_key_exists('voiceReferral', $validatedData) && $this->voiceReferral == true ? $this->voiceReferral = '321zxc' : $this->voiceReferral = false;
        array_key_exists('invoiceNumber', $validatedData) ? $this->invoiceNumber =  $validatedData['invoiceNumber'] : $this->invoiceNumber = null;
        array_key_exists('taxFree', $validatedData) ? $this->taxFree = $validatedData['taxFree'] : $this->taxFree = false;
        array_key_exists('keyed', $validatedData) ? $this->keyed = $validatedData['keyed'] : null;
        array_key_exists('CARDHOLDER_ADDRESS', $validatedData) ? $this->CARDHOLDER_ADDRESS = $validatedData['CARDHOLDER_ADDRESS'] : null;
        array_key_exists('CARDHOLDER_ZIP', $validatedData) ? $this->CARDHOLDER_ZIP = $validatedData['CARDHOLDER_ZIP'] : null;

        return response($this->posPayment());
    }

    private function saveToSdkLog(array $data, $status)
    {
        $elavonSdkTransaction = new ElavonSdkTransaction();
        $elavonSdkTransaction->transaction_id = $this->transaction_id;
        $elavonSdkTransaction->payment_gateway_id = $this->paymentGatewayId;
        $elavonSdkTransaction->chan_id = $this->chanId;
        $elavonSdkTransaction->status = $status;
        $elavonSdkTransaction->log = $data;
        $elavonSdkTransaction->save();
    }

    private function initPosTerminal()
    {
        $id = $this->startCardReaderConfiguration();

        if ($id['statusDetails'] === 'TARGET_UNAVAILABLE') {
            $msg = ['POS Terminal initialization failed.<br>Please restart Converge Service and try again.'];
            $this->saveToSdkLog($msg, 'error');

            return ['errors' => $msg];
        }

        $id = $id['data']['cardReaderCommand']['id'];

        $id = $this->startCardReadersSearch();

        do {
            sleep(1);
            $response = $this->getCardReadersSearchStatus($id);
        } while ($response['data']['cardReadersSearch']['completed'] === false);

        $this->saveToSdkLog($response, 'success');

        if (!count($response['data']['cardReadersSearch']['cardReaders'])) {
            $msg = ['POS Terminal failed to initialize properly<br>Please restart ConvergeConnect service and try again'];
            $this->saveToSdkLog($response, 'error');
            $this->saveToSdkLog($msg, 'error');
            return ['errors' => $msg];
        } else {
            return [];
        }
    }

    private function getPosTerminalStatus($autoInit = true)
    {
        $cardReaderInfo = $this->getCardReaderInfo();
        if (array_key_exists('errors', $cardReaderInfo)) {
            return $cardReaderInfo;
        } else if (array_key_exists('fatal_error', $cardReaderInfo)) {
            return $cardReaderInfo;
        }

        if ($cardReaderInfo['data']['cardReaderInfo'] === null) {
            $this->saveToSdkLog(['POS Terminal is not initialized'], 'error');
            if ($autoInit) {
                $this->saveToSdkLog(['Starting automatic initialization process...'], 'info');
                return $this->initPosTerminal();
            } else {
                return ['errors' => 'POS Terminal is not initialized'];
            }
        } else {
            return [];
        }
    }

    private function getTransactionResponse($response)
    {
        if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'FAILED') {
            $this->saveToSdkLog($response, 'failed');

            switch ($response['data']['paymentGatewayCommand']['paymentTransactionData']['errors'][0]) {
                case 'ECLCommerceError ECLCardReaderCanceled':
                    $msg = ['Transaction canceled'];
                    $this->saveToSdkLog($msg, 'declined');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionCardNeedsRemoval':
                    $msg = ['Remove the card and try again<br>Insert the card only when prompted by the POS Terminal'];
                    $this->saveToSdkLog($msg, 'declined');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionCardRemoved':
                    $msg = ['Card has been removed before the transaction completed'];
                    $this->saveToSdkLog($msg, 'declined');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLCardReaderCannotConnect':
                    $msg = ['POS Terminal disconnected<br>Please verify that POS Terminal is properly connected and try again'];
                    $this->saveToSdkLog($msg, 'declined');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLCardReaderCardDataInvalid':
                    $msg = ['Invalid card'];
                    $this->saveToSdkLog($msg, 'declined');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionCardReaderNoneAvailable':
                    $msg = ['POS Terminal is not available<br>Please verify the POS Terminal is properly connected and try again'];
                    $this->saveToSdkLog($msg, 'declined');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionInvalidTransactionRequest':
                    $msg = ['This transaction is already settled'];
                    $this->saveToSdkLog($msg, 'failed');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionNotRefundable':
                    $msg = ['This transaction isn\'t settled'];
                    $this->saveToSdkLog($msg, 'failed');
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionInvalidCardNumber':
                    $msg = ['The credit card is invalid'];
                    $this->saveToSdkLog($msg, 'failed');
                    return ['errors' => $msg];
                default:
                    $msg = ['Warning: Unhandled error occured. Please check log entry above'];
                    $this->saveToSdkLog($msg, 'declined');
                    return ['errors' => $msg];
            }
        } else if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'DECLINED') {
            $this->saveToSdkLog($response, 'declined');

            return ['errors' => 'Card declined by issuer'];
        } else if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'APPROVED') {
            $this->saveToSdkLog($response, 'approved');

            return ['success' => $response['data']['paymentGatewayCommand']['eventQueue'], 'transaction_id' => $response['data']['paymentGatewayCommand']['paymentTransactionData']['id']];
        } else {
            $this->saveToSdkLog($response, 'unhandled');

            return ['errors' => $response];
        }
    }

    public function posPayment($ignoreInitErrors = true)
    {
        $response = $this->getPosTerminalStatus();

        if (array_key_exists('fatal_error', $response)) {
            return (['errors' => $response['fatal_error']]);
        } else if (array_key_exists('errors', $response) && !$ignoreInitErrors) {
            return $response;
        }

        $response = $this->openPaymentGateway();

        if (array_key_exists('errors', $response)) {
            return $response;
        }

        if (array_key_exists('errors', $this->startPaymentTransaction())) {
            $msg = ['Error at starting transaction. Please try again.'];
            $this->saveToSdkLog($msg, 'error');
            return ['errors' => $msg];
        }

        set_time_limit(0);

        do {
            sleep(1);
            $response = $this->getPaymentTransactionStatus();

            if (array_key_exists('requiredInformation', $response['data']['paymentGatewayCommand'])) {
                if (is_array($response['data']['paymentGatewayCommand']['requiredInformation'])) {
                    switch ($response['data']['paymentGatewayCommand']['requiredInformation'][0]) {
                        case 'VoiceReferral':
                        case 'CardPresent':
                            $this->continuePaymentTransaction();
                            break;
                        default:
                            break;
                    }
                }
            }
        } while ($response['data']['paymentGatewayCommand']['completed'] === false);

        return $this->getTransactionResponse($response);
    }

    private function continuePaymentTransaction()
    {
        $payload = [
            'method' => 'continuePaymentTransaction',
            'requestId' => idate("U"),
            'targetType' => 'paymentGatewayConverge',
            'version' => '1.0',
            'parameters' => [
                'paymentGatewayId' => $this->paymentGatewayId,
                'chanId' => $this->chanId
            ]
        ];

        if ($this->keyed) {
            $payload['parameters']['CardPresent'] = false;
        }

        if ($this->voiceReferral) {
            $payload['parameters']['VoiceReferral'] = $this->voiceReferral;
        }

        return $this->sendRequest($payload);
    }

    private function sendRequest($payload, $verbose = true)
    {
        if (empty(auth()->user()->open_register)) {
            return ['errors' => ['Cash Register' => ['Your session with cash register has expired']]];
        }

        $ip = auth()->user()->open_register->cash_register->pos_terminal_ip;
        $port = auth()->user()->open_register->cash_register->pos_terminal_port;

        $url = "https://" . $ip . ':' . $port . '/rest/command';
        $client = new Client(['verify' => false]);
        if ($verbose) {
            $this->saveToSdkLog($payload, 'payload');
        }

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'connect_timeout' => 30,
                'body' => json_encode($payload)
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $this->saveToSdkLog([$e->getResponse()], 'error');
                return ['errors' => [$e]];
            } else {
                $this->saveToSdkLog([$e->getMessage()], 'error');
                return ['fatal_error' => ['Connection refused<br>Please verify the server running converge sdk is configured correctly']];
            }
        }

        $response = $response->getBody()->getContents();
        if ($verbose) {
            $this->saveToSdkLog(json_decode($response, true), 'success');
        }

        return json_decode($response, true);
    }

    private function startCardReaderConfiguration()
    {
        $payload = [
            'method' => 'startCardReaderConfiguration',
            'requestId' => idate("U"),
            'targetType' => 'cardReader',
            'version' => '1.0',
            'parameters' => [
                'cardReaderConnection' => [
                    'connectionMethod' => 'INET',
                    'inetAddress' => [
                        "host" => auth()->user()->open_register->cash_register->pos_terminal_ip,
                        "port" => auth()->user()->open_register->cash_register->pos_terminal_port,
                        "encryptionScheme" => config('elavon.posTerminal.encr')
                    ]
                ]
            ]
        ];

        return $this->sendRequest($payload);
    }

    private function getCommandStatusOnCardReader($id)
    {
        $payload = [
            "method" => "getCommandStatusOnCardReader",
            "requestId" => idate("U"),
            "targetType" => "cardReader",
            "version" => "1.0",
            "parameters" => [
                "id" => $id
            ]
        ];

        return $this->sendRequest($payload);
    }

    private function startCardReadersSearch()
    {
        $payload = [
            "method" => "startCardReadersSearch",
            "requestId" => idate("U"),
            "targetType" => "cardReader",
            "version" => "1.0",
            "parameters" => [
                "timeout" => 4500,
                "updateIfNecessary" => true,
                "connect" => true
            ]
        ];

        $response = $this->sendRequest($payload);
        $this->saveToSdkLog($response, 'info');

        return $response['requestId'];
    }

    private function getCardReadersSearchStatus($id)
    {
        $requestId = idate("U");

        $payload = [
            "method" => "getCardReadersSearchStatus",
            "requestId" => $requestId,
            "targetType" => "cardReader",
            "version" => "1.0",
            "parameters" => [
                "commandId" => $id
            ]
        ];

        return $this->sendRequest($payload);
    }

    private function getCardReaderInfo()
    {
        $requestId = idate("U");

        $payload = [
            "method" => "getCardReaderInfo",
            "requestId" => $requestId,
            "targetType" => "api"
        ];

        return $this->sendRequest($payload);
    }

    private function openPaymentGateway()
    {
        $sdkAcc = auth()->user()->open_register->cash_register->store->company->bankAccountSdk();

        $payload = [
            "method" => "openPaymentGateway",
            "requestId" => idate("U"),
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => $sdkAcc->account
        ];

        $paymentGateway = $this->sendRequest($payload);

        if (isset($paymentGateway['errors']) || isset($paymentGateway['fatal_error'])) {
            $this->saveToSdkLog(['Payment gateway failed'], 'error');
            return ['errors' => isset($paymentGateway['errors']) ? $paymentGateway['errors'] : $paymentGateway['fatal_error']];
        }

        if ($paymentGateway['data']['paymentGatewayCommand']['openPaymentGatewayData']['result'] !== 'SUCCESS') {
            $this->saveToSdkLog(['Payment gateway failed'], 'error');
            return ['errors' => 'Payment gateway failed'];
        } else {
            $this->paymentGatewayId = $paymentGateway['data']['paymentGatewayCommand']['openPaymentGatewayData']['paymentGatewayId'];
            return [];
        }
    }

    private function startPaymentTransaction()
    {
        $payload = [
            "method" => "startPaymentTransaction",
            "requestId" => idate("U"),
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $this->paymentGatewayId,
                "transactionType" => $this->selected_transaction,
                "tenderType" => "CARD",
                "cardType" => null,
                "discountAmounts" => null
            ]
        ];

        if ($this->invoiceNumber) {
            $payload['parameters']['invoiceNumber'] = $this->invoiceNumber;
        }

        if ($this->keyed) {
            $payload['parameters']['cardEntryTypes'] = ['MANUALLY_ENTERED'];

            if ($this->CARDHOLDER_ADDRESS && $this->CARDHOLDER_ZIP) {
                $payload['parameters']['avsFields'] = [
                    'CARDHOLDER_ADDRESS' => $this->CARDHOLDER_ADDRESS,
                    'CARDHOLDER_ZIP' => $this->CARDHOLDER_ZIP
                ];
            }
        }

        if ($this->voiceReferral) {
            $payload['parameters']['forceTransaction'] = true;
        }

        if ($this->taxFree) {
            $payload['parameters']['isTaxInclusive'] = false;
            $payload['parameters']['taxAmounts'] = ["value" => 0, "currencyCode" => "USD"];
        } else {
            $payload['parameters']['isTaxInclusive'] = true;
        }

        if ($this->amount) {
            $payload['parameters']['baseTransactionAmount'] = [
                "value" => (int) $this->amount,
                "currencyCode" => "USD"
            ];
        }

        if ($this->originalTransId) {
            $payload['parameters']['originalTransId'] =  $this->originalTransId;
        }

        $response = $this->sendRequest($payload);
        $this->chanId = $response['data']['paymentGatewayCommand']['chanId'];

        return [];
    }

    private function getPaymentTransactionStatus()
    {
        $payload = [
            "method" => "getPaymentTransactionStatus",
            "requestId" => idate("U"),
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $this->paymentGatewayId,
                "chanId" => $this->chanId
            ]
        ];

        return $this->sendRequest($payload, false);
    }

    private function cancelPaymentTransaction()
    {
        $payload = [
            "method" => "cancelPaymentTransaction",
            "requestId" => idate("U"),
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $this->paymentGatewayId,
                "chanId" => $this->chanId
            ]
        ];

        return $this->sendRequest($payload);
    }
}
