<?php

use Illuminate\Database\Seeder;
use App\MasAccount;

class MasAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasAccount::create([
            'environment' => 'test',
            'endpoint' => 'http://masapitest.cloudapp.net/MASDirect.Api.Service.svc/api/messagesj',
            'direct_id' => 'USZZ000035',
            'fulfiller_md_number' => 'USNY000012',
            'username' => 'pshed',
            'password' => '9JNH76k#',
            'active' => true,
        ]);

        MasAccount::create([
            'environment' => 'production',
            'endpoint' => 'https://api.masdirectnetwork.com/api/messages',
            'direct_id' => 'USZZ000035',
            'fulfiller_md_number' => 'USNY000012',
            'username' => 'pshed',
            'password' => '9JNH76k#',
            'active' => false,
        ]);
    }
}
