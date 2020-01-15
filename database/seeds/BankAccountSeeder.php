<?php

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
        $api = json_encode([
            'merchant_id' => '009710',
            'user_id' => 'convergeapi',
            'pin' => 'LWUY8K81466BXK4Y6I7FERJMOLDRM1XL37JPP4ATK3JORDUMAYDRICE9H7QVL6M8',
            'test_mode' => 'false'
        ]);
        $sdk = json_encode([
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
        ]);

        DB::table('bank_accounts')->insert([
            [
                'store_id' => 1,
                'type' => 'api',
                'account' => $api,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'store_id' => 1,
                'type' => 'sdk',
                'account' => $sdk,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'store_id' => 2,
                'type' => 'api',
                'account' => $api,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'store_id' => 2,
                'type' => 'sdk',
                'account' => $sdk,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
