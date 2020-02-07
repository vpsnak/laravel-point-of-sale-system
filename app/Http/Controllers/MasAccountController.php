<?php

namespace App\Http\Controllers;

use App\MasAccount;
use App\Order;

class MasAccountController extends Controller
{
    protected static $mas_direct_id = 'USZZ000035';
    protected static $user = 'pshed';
    protected static $pass = '9JNH76k#';

    public function test()
    {
        $masAccounts = MasAccount::all();

        return response([
            'accounts' => $masAccounts,
            'karfwtila' => [
                'user' => 'pshed',
                'pass' => '9JNH76k#',
                'FulfillerMDNumber' => 'USNY000012',
                'mas_direct_id' => 'USZZ000035',
                'test' => 'http://masapitest.cloudapp.net/MASDirect.Api.Service.svc/api/messagesj',
                'production' => 'https://api.masdirectnetwork.com/api/messages'
            ],
            'headers' => [
                'test' => $masAccounts[0]->getAuthHeader(),
                'prod' => $masAccounts[1]->getAuthHeader(),
                'snak' => 'Basic ' . base64_encode(self::$user . ':' . self::$pass . ':' . self::$mas_direct_id),
            ]
        ]);
    }

    public function getEnv()
    {
        return response(MasAccount::getActive()->environment);
    }

    public function setEnv(string $mode)
    {
        $masAccount = MasAccount::whereEnvironment($mode)->firstOrFail();
        $currentActiveAcc = MasAccount::getActive();

        if (!$masAccount->active) {
            $currentActiveAcc->active = false;
            $currentActiveAcc->save();

            $masAccount->active = true;
            $masAccount->save();
        }

        return response([
            "info" => ["MAS" => ["MAS environment: $masAccount->environment"]],
            "payload" => $masAccount->environment
        ]);
    }
}
