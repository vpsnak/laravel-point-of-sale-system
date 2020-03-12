<?php

use App\BankAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // converge = api
        // commerce sdk = sdk

        $green2greenCorpApiAcc = [
            'endpoint' => 'https://api.convergepay.com/VirtualMerchant/processxml.do',
            'ssl_merchant_id' => '2129225',
            'ssl_user_id' => 'greenapi',
            'ssl_pin' => 'VQHLWYVYB6HYFW9L2XB48H8AIT7B0XCROUNYLACC3B79B99GOO9HEPHDJOXXV7P0'
        ];

        $green2greenCorpSdkAcc = [
            'app' => 'VMM',
            'email' => 'support@webo2.gr',
            'pin' => 'VQHLWYVYB6HYFW9L2XB48H8AIT7B0XCROUNYLACC3B79B99GOO9HEPHDJOXXV7P0',
            'userId' => 'greenapi',
            'merchantId' => '2129225',
            'paymentGatewayEnvironment' => 'PROD',
            'vendorId' => 'sc100028',
            'vendorAppName' => 'Webo2',
            'vendorAppVersion' => '2.1.4',
            "retrieveAccountInfo" => true,
            "handleDigitalSignature" => true
        ];

        $plantshed87CorpApiAcc = [
            'endpoint' => 'https://api.convergepay.com/VirtualMerchant/processxml.do',
            'ssl_merchant_id' => '2129225',
            'ssl_user_id' => 'plantapi',
            'ssl_pin' => '1KLFEJNFW8R2PSFWSYJ8NJEJ8A6UFJQADTEJOXO43PWO6R4I9GD34YTAVSAU2EQA',
        ];

        $plantshed87CorpSdkAcc = [
            'app' => 'VMM',
            'email' => 'support@webo2.gr',
            'pin' => '1KLFEJNFW8R2PSFWSYJ8NJEJ8A6UFJQADTEJOXO43PWO6R4I9GD34YTAVSAU2EQA',
            'userId' => 'plantapi',
            'merchantId' => '2129225',
            'paymentGatewayEnvironment' => 'PROD',
            'vendorId' => 'sc100028',
            'vendorAppName' => 'Webo2',
            'vendorAppVersion' => '2.1.4',
            "retrieveAccountInfo" => true,
            "handleDigitalSignature" => true,
        ];

        $plantshedPrinceIncApiAcc = [
            'endpoint' => 'https://api.convergepay.com/VirtualMerchant/processxml.do',
            'ssl_merchant_id' => '2129225',
            'ssl_user_id' => 'plantshedapi',
            'ssl_pin' => '29T9DK6YJS29VNBOCLVR0YWZ0XLJXSL5366XMEGVILHGKBN2S6YZKGC27FTADR4N'
        ];

        $plantshedPrinceIncSdkAcc = [
            'app' => 'VMM',
            'email' => 'support@webo2.gr',
            'pin' => '29T9DK6YJS29VNBOCLVR0YWZ0XLJXSL5366XMEGVILHGKBN2S6YZKGC27FTADR4N',
            'userId' => 'plantshedapi',
            'merchantId' => '2129225',
            'paymentGatewayEnvironment' => 'PROD',
            'vendorId' => 'sc100028',
            'vendorAppName' => 'Webo2',
            'vendorAppVersion' => '2.1.4',
            "retrieveAccountInfo" => true,
            "handleDigitalSignature" => true,
        ];

        $demoApiAcc = [
            'endpoint' => 'https://api.demo.convergepay.com/VirtualMerchantDemo/processxml.do',
            'ssl_merchant_id' => '009710',
            'ssl_user_id' => 'convergeapi',
            'ssl_pin' => 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8'
        ];

        $demoSdkAcc = [
            'app' => 'VMM',
            'email' => 'support@webo2.gr',
            'pin' => 'H3192LKKOIBN3GIZ9FGXX01MYWGIYEJ2UJ8PW4PCHXRSNBZVFMF1PWNG0HR003MI',
            'userId' => 'convergeapi',
            'merchantId' => '009710',
            'paymentGatewayEnvironment' => 'DEMO',
            'vendorId' => 'sc100028',
            'vendorAppName' => 'Webo2',
            'vendorAppVersion' => '2.1.4',
            "retrieveAccountInfo" => true,
            "handleDigitalSignature" => true,
        ];

        // Green 2 Green Corp
        $green2greenCorpApi = [
            'company_id' => 1,
            'type' => 'api',
            'account' => $green2greenCorpApiAcc
        ];

        $green2greenCorpSdk = [
            'company_id' => 1,
            'type' => 'sdk',
            'account' => $green2greenCorpSdkAcc
        ];
        // Plantshed 87 Corp
        $plantshed87CorpApi = [
            'company_id' => 2,
            'type' => 'api',
            'account' => $plantshed87CorpApiAcc
        ];
        $plantshed87CorpSdk = [
            'company_id' => 2,
            'type' => 'sdk',
            'account' => $plantshed87CorpSdkAcc
        ];
        // Plantshed Prince Inc
        $plantshedPrinceIncApi = [
            'company_id' => 3,
            'type' => 'api',
            'account' => $plantshedPrinceIncApiAcc
        ];
        $plantshedPrinceIncSdk = [
            'company_id' => 3,
            'type' => 'sdk',
            'account' => $plantshedPrinceIncSdkAcc
        ];
        // demo
        $demoApi = [
            'company_id' => 4,
            'type' => 'api',
            'account' => $demoApiAcc
        ];

        $demoSdk = [
            'company_id' => 4,
            'type' => 'sdk',
            'account' => $demoSdkAcc
        ];

        // plantshed 96 (green2green)
        BankAccount::create($green2greenCorpApi);
        BankAccount::create($green2greenCorpSdk);
        // plantshed 87
        BankAccount::create($plantshed87CorpApi);
        BankAccount::create($plantshed87CorpSdk);
        // plantshed prince street
        BankAccount::create($plantshedPrinceIncApi);
        BankAccount::create($plantshedPrinceIncSdk);
        // demo acc
        BankAccount::create($demoApi);
        BankAccount::create($demoSdk);
    }
}
