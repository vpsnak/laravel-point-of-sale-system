<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosTerminalController extends Controller
{
    public function makePayment(Request $request)
    {
        $startPaymentTransaction = [
            'method' => 'startPaymentTransaction',
            'requestId' => '32878540',
            'targetType' => 'paymentGatewayConverge',
            'version' => '1.0',
            'parameters' => [
                'paymentGatewayId' => '11b0032b-eb0d-4d9a-8664-eb430684cb92',
                'transactionType' => 'SALE',
                'baseTransactionAmount' => [
                    'value' => 2000,
                    'currencyCode' => 'USD'
                ],
                'tenderType' => 'CARD',
                'cardType' => null,
                'isTaxInclusive' => false,
                'taxAmounts' => [
                    "value" => 0,
                    "currencyCode" => "USD"
                ],
                "discountAmounts" => null,
            ],
        ];

        $getStatus = [
            "method" => "getPaymentTransactionStatus",
            "requestId" => "32878541",
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => "11b0032b-eb0d-4d9a-8664-eb430684cb92",
                "chanId" => "239c9b47-c6c3-462b-8701-b83586bdf3e6"
            ]

        ];

        return response($startPaymentTransaction);
    }

    public function getPaymentStatus(Request $request)
    {
        $validatedData = $request->validate([
            'paymentGatewayId' => 'required|string',
            'chanId' => 'required|string'
        ]);

        $transactionStatus = [
            "method" => "getPaymentTransactionStatus",
            "requestId" => "32878541",
            "targetType" => "paymentGatewayConverge",
            "version" => "1.0",
            "parameters" => [
                "paymentGatewayId" => "11b0032b-eb0d-4d9a-8664-eb430684cb92",
                "chanId" => "239c9b47-c6c3-462b-8701-b83586bdf3e6"
            ]
        ];

        $incompleteResponse = [
            "requestId" => "32878540",
            "statusDetails" => "REQUEST_ACCEPTED",
            "data" => [
                "paymentGatewayCommand" => [
                    "completed" => false,
                    "eventQueue" => [
                        "timeStamp" => "1457115273607",
                        "statusDetails" => "STARTING"
                    ],
                    "chanId" => "239c9b47-c6c3-462b-8701-b83586bdf3e6"
                ]
            ]
        ];

        $emvSuccessfulResponse = [
            "requestId" => "1947227770",
            "statusDetails" => "REQUEST_ACCEPTED",
            "data" => [
                "paymentGatewayCommand" => [
                    "completed" => true,
                    "eventQueue" => [],
                    "chanId" => "7c332149-ba08-497d-9c7f-ab57792dac1f",
                    "paymentTransactionData" => [
                        "result" => "APPROVED",
                        "authCode" => "******",
                        "iccAid" => "A0000000031010",
                        "iccAppName" => "Visa Credit",
                        "iccMode" => "ICC_MODE",
                        "iccTvr" => "80C0008000",
                        "iccTsi" => "6800",
                        "resultMessage" => "APPROVAL",
                        "iccCvmr" => "SIGNATURE",
                        "date" => "Fri Mar 04 13=>21=>39 MST 2016",
                        "cardEntryType" => "EMV_CONTACT",
                        "cardScheme" => "VISA",
                        "amount" => [
                            "currencyCode" => "USD",
                            "value" => 8700
                        ],
                        "id" => "040316A15-F48AB876-9635-4933-B773-136888A3D972",
                        "transactionType" => "SALE",
                        "approved" => "yes",
                        "errors" => [],
                        "tax" => [
                            "currencyCode" => "USD",
                            "value" => 200
                        ],
                        "maskedPan" => "******",
                        "gratuityAmount" => [
                            "currencyCode" => "USD",
                            "value" => 300
                        ],
                        "signatureBitmap" => [
                            "data" => "` +J  !_ P____ X _'___ _& _'  ( _' _/ _'  ( _/ _/  ( _7 _/  ( _7 _/ _7 _7  ( _/ _/ _/ _/ _'  ( _& _%___ _'_______________________W____ X__W_ P____ Y_ P_ X  !_ X  \"  !  )  !  )  )  )  )  )  1  )  1  *  )  (  1  )  1  )  )  (  )  )  (  )  !p`#+Q  !  0  !  0  0  )  8  8  0  8  )  0 _/  0  0 _'  ( _&  (___ _& _'______ _&___ _'___ _&___ _'___ _'___ _& _'___ _/___ _' _/ _& _'p`'&__ X _&p`+)?_ P____ X___________W_ X________^_________ _'___ _'___ _/ _' _/  ( _/  0  8  (  (  1  (  )  0  )  )  )  )  )  !  )  !  !_ Y  !  !_ Y  !_ Y_ Y_ Y_ Y_ X_ Y_ Xp`-*( _'_ X_______ X _'_ X _'_ X _'_ X _'___ _'______ _'__^ _' _' _' _/ _' _/ _/  0 _/  0  (  0  (  0  1  0  1  (  1  )  1  )  )  )  !  )  !_ Z  !_ Y_ Y_ Q_ Z_ Q_ P_ Q_ Q_ Q_ H_ Q_ P_ Q_ P_ P__W_ X_ P___p",
                            "format" => "SIG_BIN_2"
                        ],
                        "tenderType" => "CARD",
                        "balanceDue" => [
                            "currencyCode" => "UNKNOWN",
                            "value" => 0
                        ]
                    ]
                ]
            ]
        ];

        $failedPaymentResponse = [
            "requestId" => "670817917",
            "statusDetails" => "REQUEST_ACCEPTED",
            "data" => [
                "paymentGatewayCommand" => [
                    "completed" => true,
                    "eventQueue" => [
                        "timeStamp" => "1447437024243",
                        "statusDetails" => "CARD_READER_TRANSACTION_COMPLETED"
                    ],
                    "chanId" => "481c043e-78ad-410b-bb48-44ee2526be2a",
                    "paymentTransactionData" => [
                        "id" => "131115CAD-A66E04AD-E559-4A7D-AF29-17E2BA8D4A77",
                        "result" => "DECLINED",
                        "authCode" => "******",
                        "transactionType" => "SALE",
                        "approved" => "no",
                        "errors" => [],
                        "maskedPan" => "******",
                        "tenderType" => "CREDIT_CARD",
                        "date" => "Fri Nov 13 12=>50=>19 MST 2015",
                        "cardScheme" => "VISA"
                    ]
                ]
            ]
        ];

        return response();
    }
}
