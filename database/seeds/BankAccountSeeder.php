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
        $green2greenCorpApiAcc = [
            'merchant_id' => '009710',
            'user_id' => 'convergeapi',
            'pin' => 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8',
            'test_mode' => 'false'
        ];

        $green2greenCorpSdkAcc = [
            'app' => 'VMM',
            'email' => 'support@webo2.gr',
            'pin' => 'H3192LKKOIBN3GIZ9FGXX01MYWGIYEJ2UJ8PW4PCHXRSNBZVFMF1PWNG0HR003MI',
            'userId' => 'convergeapi',
            'merchantId' => '009710',
            'paymentGatewayEnvironment' => 'PROD',
            'vendorId' => 'sc100028',
            'vendorAppName' => 'Webo2',
            'vendorAppVersion' => '2.1.4',
            "retrieveAccountInfo" => true,
            "handleDigitalSignature" => true,
        ];

        $plantshed87CorpApiAcc = [
            'merchant_id' => '009710',
            'user_id' => 'convergeapi',
            'pin' => 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8',
            'test_mode' => 'false'
        ];

        $plantshed87CorpSdkAcc = [
            'app' => 'VMM',
            'email' => 'support@webo2.gr',
            'pin' => '1KLFEJNFW8R2PSFWSYJ8NJEJ8A6UFJQADTEJOXO43PWO6R4I9GD34YTAVSAU2EQA',
            'userId' => 'plantapi',
            'merchantId' => '009710',
            'paymentGatewayEnvironment' => 'PROD',
            'vendorId' => 'sc100028',
            'vendorAppName' => 'Webo2',
            'vendorAppVersion' => '2.1.4',
            "retrieveAccountInfo" => true,
            "handleDigitalSignature" => true,
        ];

        $plantshedPrinceIncApiAcc = [
            'merchant_id' => '009710',
            'user_id' => 'plantapi',
            'pin' => 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8',
            'test_mode' => 'false'
        ];

        $plantshedPrinceIncSdkAcc = [
            'app' => 'VMM',
            'email' => 'support@webo2.gr',
            'pin' => 'H3192LKKOIBN3GIZ9FGXX01MYWGIYEJ2UJ8PW4PCHXRSNBZVFMF1PWNG0HR003MI',
            'userId' => 'Plantshed',
            'merchantId' => '009710',
            'paymentGatewayEnvironment' => 'PROD',
            'vendorId' => 'sc100028',
            'vendorAppName' => 'Webo2',
            'vendorAppVersion' => '2.1.4',
            "retrieveAccountInfo" => true,
            "handleDigitalSignature" => true,
        ];

        $demoApiAcc = [
            'merchant_id' => '009710',
            'user_id' => 'convergeapi',
            'pin' => 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8',
            'test_mode' => 'false'
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
            'company_id' => 1,
            'type' => 'api',
            'account' => $plantshed87CorpApiAcc
        ];
        $plantshed87CorpSdk = [
            'company_id' => 1,
            'type' => 'sdk',
            'account' => $plantshed87CorpSdkAcc
        ];
        // Plantshed Prince Inc
        $plantshedPrinceIncApi = [
            'company_id' => 1,
            'type' => 'api',
            'account' => $plantshedPrinceIncApiAcc
        ];
        $plantshedPrinceIncSdk = [
            'company_id' => 1,
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

        BankAccount::create($bankAcc3);
        BankAccount::create($bankAcc4);
        // demo acc
        BankAccount::create($demoApi);
        BankAccount::create($demoSdk);
    }
}
