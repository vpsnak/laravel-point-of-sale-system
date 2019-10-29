<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use \App\Payment;
use \App\ElavonSdkPayment;
use \App\TransactionLog;

class PosTerminalController extends Controller
{
    private $payment;

    public function __construct(Payment $payment)
    {
        $this->setPayment($payment);
    }

    protected function getPayment()
    {
        return $this->payment;
    }

    protected function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    private function saveToSdkLog($testCase, $data)
    {
        $elavonSdkPayment = new ElavonSdkPayment();

        $elavonSdkPayment->payment_id = $this->$this->getPayment()->id;
        $elavonSdkPayment->cash_register_id = $this->$this->getPayment()->cash_register_id;
        $elavonSdkPayment->test_case = $testCase;
        $elavonSdkPayment->log = $data;

        $elavonSdkPayment->save();
    }

    private function saveToLog(Payment $payment, $data)
    {
        $transactionLog = new TransactionLog();

        $transactionLog->payment_id = $payment->id;
        $transactionLog->cash_register_id = $payment->cash_register_id;
        $transactionLog->log = $data;

        $transactionLog->save();
    }

    private function setPaymentStatus($status)
    {
        $this->getPayment()->status = $status;
        $this->getPayment()->save();
    }

    private function initPosTerminal()
    {
        $cardReaderInfo = json_decode(self::getCardReaderInfo(), true);
        if ($cardReaderInfo['data']['cardReaderInfo'] === null) {
            // card reader is not initialized!

            $id = json_decode(self::startCardReaderConfiguration(), true);

            if ($id['statusDetails'] === 'TARGET_UNAVAILABLE') {
                self::setPaymentStatus($this->getPayment(), 'failed');

                return ['errors' => 'POS Terminal is already initialized or not available'];
            }

            $id = $id['data']['cardReaderCommand']['id'];

            $id = json_decode(self::startCardReadersSearch(), true);
            $id = $id['requestId'];

            do {
                sleep(1);
                $response = json_decode(self::getCardReadersSearchStatus($id), true);
            } while ($response['data']['cardReadersSearch']['completed'] === false);

            if (!count($response['data']['cardReadersSearch']['cardReaders'])) {
                self::setPaymentStatus('failed');

                return ['errors' => 'POS Terminal failed to initialize properly'];
            }
        }
    }

    private function getTransactionResponse($response)
    {
        if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'FAILED') {
            self::setPaymentStatus('failed');

            switch ($response['data']['paymentGatewayCommand']['paymentTransactionData']['errors'][0]) {
                case 'ECLCommerceError ECLCardReaderCanceled':
                    return ['errors' => 'Transaction canceled'];
                    break;
                case 'ECLCommerceError ECLTransactionCardNeedsRemoval':
                    return ['errors' => 'Remove the card and try again.<br>Insert the card only when prompted by the POS Terminal'];
                    break;

                case 'ECLCommerceError ECLTransactionCardRemoved':
                    return ['errors' => 'Card has been removed before the transaction completed'];
                    break;
                case 'ECLCommerceError ECLCardReaderCannotConnect':
                    return ['errors' => 'POS Terminal disconnected<br>Please verify that POS Terminal is properly connected and try again'];
                    break;
                case 'ECLCommerceError ECLCardReaderCardDataInvalid':
                    return ['errors' => 'Invalid card'];
                    break;
                default:
                    return ['errors' => $response['data']['paymentGatewayCommand']['paymentTransactionData']];
            }
        } else if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'DECLINED') {
            self::setPaymentStatus('declined');

            return ['errors' => 'Card declined by issuer'];
        } else if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result'] === 'APPROVED') {
            self::setPaymentStatus('approved');

            return ['success' => $response['data']['paymentGatewayCommand']['eventQueue']];
        } else {
            self::setPaymentStatus('failed');

            return ['errors' => $response];
        }
    }

    public function posPayment($amount)
    {
        self::setPaymentStatus('failed');
        self::initPosTerminal();
        $amount *= 100;

        $paymentGatewayId = json_decode(self::openPaymentGateway(), true);

        if ($paymentGatewayId['data']['paymentGatewayCommand']['openPaymentGatewayData']['result'] !== 'SUCCESS') {
            self::setPaymentStatus('failed');

            return ['errors' => 'Payment gateway failed'];
        }

        $paymentGatewayId = $paymentGatewayId['data']['paymentGatewayCommand']['openPaymentGatewayData']['paymentGatewayId'];
        $chanId = json_decode(self::startPaymentTransaction($paymentGatewayId, $amount), true);

        $chanId = $chanId['data']['paymentGatewayCommand']['chanId'];

        set_time_limit(300);

        do {
            sleep(1);
            $response = json_decode(self::getPaymentTransactionStatus($paymentGatewayId, $chanId), true);
        } while ($response['data']['paymentGatewayCommand']['completed'] === false);

        self::getTransactionResponse($response);
    }

    private  function sendRequest($payload)
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
            self::setPaymentStatus($payload, 'failed');
            return ['errors' => $e->getResponse()];
        }

        return $response->getBody()->getContents();
    }

    private  function startCardReaderConfiguration()
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

        return self::sendRequest($payload);
    }

    private  function getCommandStatusOnCardReader($id)
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

        return self::sendRequest($payload);
    }

    private  function startCardReadersSearch()
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

        return self::sendRequest($payload);
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

        return self::sendRequest($payload);
    }

    private  function getCardReaderInfo()
    {
        $requestId = idate("U");

        $payload = [
            "method" => "getCardReaderInfo",
            "requestId" => $requestId,
            "targetType" => "api"
        ];

        return self::sendRequest($payload);
    }

    private  function openPaymentGateway()
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

        return self::sendRequest($payload);
    }

    private  function startPaymentTransaction($paymentGatewayId, $transactionAmount)
    {
        $requestId = idate("U");

        $payload = [
            "method" => "startPaymentTransaction",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $paymentGatewayId,
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

        return self::sendRequest($payload);
    }

    private  function getPaymentTransactionStatus($paymentGatewayId, $chanId)
    {
        $requestId = idate("U");

        $payload = [
            "method" => "getPaymentTransactionStatus",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $paymentGatewayId,
                "chanId" => $chanId
            ]
        ];

        return self::sendRequest($payload);
    }

    private  function cancelPaymentTransaction($paymentGatewayId, $chanId)
    {
        $requestId = idate("U");

        $payload = [
            "method" => "cancelPaymentTransaction",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $paymentGatewayId,
                "chanId" => $chanId
            ]
        ];

        return self::sendRequest($payload);
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

        return self::sendRequest($payload);
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

        return self::sendRequest($payload);
    }
}
