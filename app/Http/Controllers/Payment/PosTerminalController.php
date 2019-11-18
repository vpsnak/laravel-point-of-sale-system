<?php

namespace App\Http\Controllers;

use App\ElavonSdkPayment;
use App\Payment;
use GuzzleHttp\Client;

class PosTerminalController extends Controller
{
    private $payment;
    private $testCase;
    private $transactionId;
    private $paymentGatewayId;
    private $chanId;

    public function __construct(Payment $payment, $amount, $testCase = null)
    {
        $this->payment = $payment;
        $this->testCase = $testCase;
    }

    private function saveToSdkLog($data, $status)
    {
        $elavonSdkPayment = new ElavonSdkPayment();

        $elavonSdkPayment->payment_id = $this->payment->id;
        $elavonSdkPayment->cash_register_id = $this->payment->cash_register_id;
        $elavonSdkPayment->payment_gateway_id = $this->paymentGatewayId;
        $elavonSdkPayment->chan_id = $this->chanId;

        $elavonSdkPayment->test_case = $this->testCase;
        $elavonSdkPayment->status = $status;
        $elavonSdkPayment->log = is_Array($data) ? json_encode($data) : strip_tags($data);

        $elavonSdkPayment->save();
    }

    private function setPaymentStatus($status)
    {
        $this->payment->status = $status;
        $this->payment->save();
    }

    private function initPosTerminal()
    {
        $id = $this->startCardReaderConfiguration();

        if ($id['statusDetails'] === 'TARGET_UNAVAILABLE') {
            $this->setPaymentStatus('failed');
            $msg = 'POS Terminal initialization failed.<br>Please restart Converge Service and try again.';
            $this->saveToSdkLog($msg, 'error');

            return ['errors' => $msg];
        }

        $id = $id['data']['cardReaderCommand']['id'];

        $response = $this->startCardReadersSearch();
        $this->saveToSdkLog($response, 'info');

        $id = $response['requestId'];

        do {
            sleep(1);
            $response = $this->getCardReadersSearchStatus($id);
        } while ($response['data']['cardReadersSearch']['completed'] === false);

        $this->saveToSdkLog($response, 'success');

        if (!count($response['data']['cardReadersSearch']['cardReaders'])) {
            $this->setPaymentStatus('failed');

            return ['errors' => 'POS Terminal failed to initialize properly'];
        }
    }

    private function getPosTerminalStatus($autoInit = true)
    {
        $cardReaderInfo = $this->getCardReaderInfo();

        if ($cardReaderInfo['data']['cardReaderInfo'] === null) {
            $this->saveToSdkLog('POS Terminal is not initialized.', 'error');
            if ($autoInit) {
                $this->saveToSdkLog('Starting automatic initialization process...', 'info');
                return $this->initPosTerminal();
            } else {
                return ['errors' => 'POS Terminal is not initialized'];
            }
        }
    }

    private function getTransactionResponse($response)
    {
        if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'FAILED') {
            $this->setPaymentStatus('failed');
            $this->saveToSdkLog($response, 'failed');

            switch ($response['data']['paymentGatewayCommand']['paymentTransactionData']['errors'][0]) {
                case 'ECLCommerceError ECLCardReaderCanceled':
                    $msg = 'Transaction canceled';
                    $this->setPaymentStatus(strip_tags($msg));
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionCardNeedsRemoval':
                    $msg = 'Remove the card and try again<br>Insert the card only when prompted by the POS Terminal';
                    $this->setPaymentStatus(strip_tags($msg));
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionCardRemoved':
                    $msg = 'Card has been removed before the transaction completed';
                    $this->setPaymentStatus(strip_tags($msg));
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLCardReaderCannotConnect':
                    $msg = 'POS Terminal disconnected<br>Please verify that POS Terminal is properly connected and try again';
                    $this->setPaymentStatus(strip_tags($msg));
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLCardReaderCardDataInvalid':
                    $msg = 'Invalid card';
                    $this->setPaymentStatus(strip_tags($msg));
                    return ['errors' => $msg];
                case 'ECLCommerceError ECLTransactionCardReaderNoneAvailable':
                    $msg = 'POS Terminal is not available<br>Please verify the POS Terminal is properly connected and try again';
                    $this->setPaymentStatus(strip_tags($msg));
                    return ['errors' => $msg];
                default:
                    $msg = 'Warning: Unhandled error occured. Please check log file entry above';
                    $this->setPaymentStatus(strip_tags($msg));
                    return ['errors' => $msg];
            }
        } else if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'DECLINED') {
            $this->setPaymentStatus('declined');
            $this->saveToSdkLog($response, 'declined');

            return ['errors' => 'Card declined by issuer'];
        } else if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'APPROVED') {
            $this->setPaymentStatus('approved');
            $this->saveToSdkLog($response, 'approved');

            return ['success' => $response['data']['paymentGatewayCommand']['eventQueue']];
        } else {
            $this->setPaymentStatus('failed');
            $this->saveToSdkLog($response, 'failed');

            return ['errors' => $response];
        }
    }

    public function posPayment($amount)
    {
        $this->setPaymentStatus('failed');
        $this->getPosTerminalStatus();
        $amount *= 100;

        $errors = $this->openPaymentGateway();

        if (array_key_exists('errors', $errors)) {
            return $errors;
        }

        if (array_key_exists('errors', $this->startPaymentTransaction($amount))) {
            $msg = 'Error at starting transaction. Please try again.';
            $this->saveToSdkLog($msg, 'error');
            return ['errors' => $msg];
        }

        set_time_limit(300);

        do {
            sleep(1);
            $response = $this->getPaymentTransactionStatus();
        } while ($response['data']['paymentGatewayCommand']['completed'] === false);
        $this->saveToSdkLog($response, 'success');

        $response = $this->getTransactionResponse($response);

        return $response;
    }

    private function sendRequest($payload, $verbose = true)
    {
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';
        $client = new Client(['verify' => false]);

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'connect_timeout' => 30,
                'body' => json_encode($payload)
            ]);
        } catch (Exception $e) {
            $this->setPaymentStatus($payload, 'failed');
            $this->saveToSdkLog($e->getResponse(), 'error');

            return ['errors' => $e->getResponse()];
        }

        $response = $response->getBody()->getContents();
        if ($verbose) {
            $this->saveToSdkLog($response, 'success');
        }

        return json_decode($response, true);
    }

    private function startCardReaderConfiguration()
    {
        $requestId = idate("U");

        $payload = [
            'method' => 'startCardReaderConfiguration',
            'requestId' => $requestId,
            'targetType' => 'cardReader',
            'version' => '1.0',
            'parameters' => [
                'cardReaderConnection' => [
                    'connectionMethod' => 'INET',
                    'inetAddress' => [
                        "host" => config('elavon.posTerminal.host'),
                        "port" => config('elavon.posTerminal.port'),
                        "encryptionScheme" => config('elavon.posTerminal.encr')
                    ]
                ]
            ]
        ];

        return $this->sendRequest($payload);
    }

    private function getCommandStatusOnCardReader($id)
    {
        $requestId = idate("U");

        $payload = [
            "method" => "getCommandStatusOnCardReader",
            "requestId" => $requestId,
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
        $requestId = idate("U");

        $payload = [
            "method" => "startCardReadersSearch",
            "requestId" => $requestId,
            "targetType" => "cardReader",
            "version" => "1.0",
            "parameters" => [
                "timeout" => 4500,
                "updateIfNecessary" => true,
                "connect" => true
            ]
        ];

        return $this->sendRequest($payload);
    }

    private  function getCardReadersSearchStatus($id)
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

        return $this->sendRequest($payload, false);
    }

    private  function getCardReaderInfo()
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
        $requestId = idate("U");

        $payload = [
            "method" => "openPaymentGateway",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "app" => config('elavon.gateway.app'),
                "email" => config('elavon.gateway.email'),
                "pin" => config('elavon.gateway.pin'),
                "userId" => config('elavon.gateway.userId'),
                "merchantId" => config('elavon.gateway.merchantId'),
                "retrieveAccountInfo" => true,
                "handleDigitalSignature" => true,
                "paymentGatewayEnvironment" => config('elavon.gateway.paymentGatewayEnvironment'),
                "vendorId" => config('elavon.gateway.vendorId'),
                "vendorAppName" => config('elavon.gateway.vendorAppName'),
                "vendorAppVersion" => config('elavon.gateway.vendorAppVersion')
            ]
        ];

        $paymentGateway = $this->sendRequest($payload);

        if ($paymentGateway['data']['paymentGatewayCommand']['openPaymentGatewayData']['result'] !== 'SUCCESS') {
            $this->setPaymentStatus('failed');
            $this->saveToSdkLog('Payment gateway failed', 'error');

            return ['errors' => 'Payment gateway failed'];
        } else {
            $this->paymentGatewayId = $paymentGateway['data']['paymentGatewayCommand']['openPaymentGatewayData']['paymentGatewayId'];

            return [];
        }
    }

    private  function startPaymentTransaction($transactionAmount)
    {
        $requestId = idate("U");

        $payload = [
            "method" => "startPaymentTransaction",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $this->paymentGatewayId,
                "transactionType" => "SALE",
                "baseTransactionAmount" => [
                    "value" => (int) $transactionAmount,
                    "currencyCode" => "USD"
                ],
                "tenderType" => "CARD",
                "cardType" => null,
                "isTaxInclusive" => true,
                "discountAmounts" => null
            ]
        ];

        $response = $this->sendRequest($payload);
        $this->chanId = $response['data']['paymentGatewayCommand']['chanId'];

        return [];
    }

    private  function getPaymentTransactionStatus()
    {
        $requestId = idate("U");

        $payload = [
            "method" => "getPaymentTransactionStatus",
            "requestId" => $requestId,
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
        $requestId = idate("U");

        $payload = [
            "method" => "cancelPaymentTransaction",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $this->paymentGatewayId,
                "chanId" => $this->chanId
            ]
        ];

        return $this->sendRequest($payload);
    }

    private  function searchPaymentTransaction($parameters)
    {
        // params example
        // $parameters = [
        //     "first6CC" => null,
        //     "creditCard" => null,
        //     "utcDate" => null,
        //     "paymentGatewayId" => $paymentGatewayId,
        //     "transId" => null,
        //     "endDate" => "20160307",
        //     "last4CC" => "4243",
        //     "beginDate" => "20160307",
        //     "note" => ""
        // ];

        if (!count($parameters)) {
            return ['errors' => 'No parameters specified'];
        }

        $requestId = idate("U");

        $payload = [
            "method" => "searchPaymentTransaction",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
        ];

        $payload[] = $parameters;

        return $this->sendRequest($payload);
    }

    private  function linkedRefund(Payment $payment)
    {
        $requestId = idate("U");

        $payload = [
            "method" => "startPaymenTransaction",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => "e9b2f3cd-ad49-482b-9982-d39d76a79a7f",
                "originalTransId" => "070316A15-0A81D9AD-6700-4E07-A82D-DF17E41F91A6",
                "transactionType" => "LINKED_REFUND",
                "tenderType" => "CARD",
                "baseTransactionAmount" => [
                    "value" => 2500,
                    "currencyCode" => "USD"
                ],
            ]
        ];

        return $this->sendRequest($payload);
    }
}