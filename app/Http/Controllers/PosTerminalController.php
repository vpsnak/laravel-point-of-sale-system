<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PosTerminalController extends Controller
{
    public static function posPayment($amount)
    {
        $amount *= 100;

        $cardReaderInfo = json_decode(self::getCardReaderInfo(), true);

        // if ($cardReaderInfo['data']['cardReaderInfo'] === null) {
        //     // card reader is not initialized!

        //     $id = json_decode(self::startCardReaderConfiguration(), true);

        //     $id = $id['data']['cardReaderCommand']['id'];

        //     do {
        //         $response = json_decode(self::getCardReadersSearchStatus($id), true);
        //     } while ($response['data']['cardReadersSearch']['completed'] === false);

        //     if (!count($response['data']['cardReadersSearch']['cardReaders'])) {
        //         return response('pos failed to init properly');
        //     }
        // }

        // pos terminal is configured
        $paymentGatewayId = json_decode(self::openPaymentGateway(), true);

        if ($paymentGatewayId['data']['paymentGatewayCommand']['openPaymentGatewayData']['result'] !== 'SUCCESS') {
            return response('payment gateway failed');
        }

        $paymentGatewayId = $paymentGatewayId['data']['paymentGatewayCommand']['openPaymentGatewayData']['paymentGatewayId'];
        $chanId = json_decode(self::startPaymentTransaction($paymentGatewayId, $amount), true);

        $chanId = $chanId['data']['paymentGatewayCommand']['chanId'];


        do {
            $response = json_decode(self::getPaymentTransactionStatus($paymentGatewayId, $chanId), true);
        } while ($response['data']['paymentGatewayCommand']['completed'] === false);


        if ($response['data']['paymentGatewayCommand']['paymentTransactionData']['result']) {
            return response('payment succeded', 200);
        }
    }

    public static function startCardReaderConfiguration()
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

        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';
        $client = new Client(['verify' => false]);

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return $response->getBody()->getContents();
    }

    public static function getCommandStatusOnCardReader($id)
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

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return  $response->getBody()->getContents();
    }

    public static function startCardReadersSearch()
    {
        $requestId = idate("U");

        $payload = [
            "method" => "startCardReadersSearch",
            "requestId" => $requestId,
            "targetType" => "cardReader",
            "version" => "1.0",
            "parameters" => [
                "timeout" => 9000,
                "updateIfNecessary" => true,
                "connect" => true
            ]
        ];

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return  $response->getBody()->getContents();
    }

    public static function getCardReadersSearchStatus($id)
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

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return  $response->getBody()->getContents();
    }

    public static function getCardReaderInfo()
    {
        $requestId = idate("U");

        $payload = [
            "method" => "getCardReaderInfo",
            "requestId" => $requestId,
            "targetType" => "api"
        ];

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return  $response->getBody()->getContents();
    }

    public static function openPaymentGateway()
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

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return  $response->getBody()->getContents();
    }

    public static function startPaymentTransaction($paymentGatewayId, $transactionAmount)
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

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return  $response->getBody()->getContents();
    }

    public static function getPaymentTransactionStatus($paymentGatewayId, $chanId)
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

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return  $response->getBody()->getContents();
    }
}
