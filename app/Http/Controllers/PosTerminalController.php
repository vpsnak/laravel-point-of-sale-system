<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Storage;

class PosTerminalController extends Controller
{
    public function startCardReaderConfiguration()
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
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }

    public function getCommandStatusOnCardReader(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required'
        ]);

        $requestId = idate("U");

        $payload = [
            "method" => "getCommandStatusOnCardReader",
            "requestId" => $requestId,
            "targetType" => "cardReader",
            "version" => "1.0",
            "parameters" => [
                "id" => $validatedData['id']
            ]
        ];

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }

    public function startCardReadersSearch()
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
                "connect" => true,
                "cardReaderConnection" => [
                    "connectionMethod" => "INET",
                    'inetAddress' => [
                        "host" => config('elavon.posTerminal.host'),
                        "port" => config('elavon.posTerminal.port'),
                        "encryptionScheme" => config('elavon.posTerminal.encr')
                    ]
                ]
            ]
        ];

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }

    public function getCardReadersSearchStatus(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required'
        ]);

        $requestId = idate("U");

        $payload = [
            "method" => "getCardReadersSearchStatus",
            "requestId" => $requestId,
            "targetType" => "cardReader",
            "version" => "1.0",
            "parameters" => [
                "commandId" => $validatedData['id']
            ]
        ];

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }

    public function getCardReaderInfo()
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
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }

    public function openPaymentGateway()
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
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }

    public function startPaymentTransaction(Request $request)
    {
        $validatedData = $request->validate([
            'paymentGatewayId' => 'required|string',
            'transactionAmount' => 'required|numeric',
        ]);

        $requestId = idate("U");

        $payload = [
            "method" => "startPaymentTransaction",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $validatedData['paymentGatewayId'],
                "transactionType" => "SALE",
                "baseTransactionAmount" => [
                    "value" => (int) $validatedData['transactionAmount'],
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
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }

    public function getPaymentTransactionStatus(Request $request)
    {
        $validatedData = $request->validate([
            "paymentGatewayId" => 'required|string',
            "chanId" => 'required|string'
        ]);

        $requestId = idate("U");

        $payload = [
            "method" => "getPaymentTransactionStatus",
            "requestId" => $requestId,
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => $validatedData['paymentGatewayId'],
                "chanId" => $validatedData['chanId']
            ]
        ];

        $client = new Client(['verify' => false]);
        $url = config('elavon.hostPC.ip') . ':' . config('elavon.hostPC.port') . '/rest/command';

        $response = $client->post($url, [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => json_encode($payload)
        ]);

        return response($response->getBody(), 200);
    }
}
