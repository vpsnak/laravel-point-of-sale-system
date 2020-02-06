<?php

namespace App\Http\Controllers;

use App\MasAccount;
use App\Order;

class MasAccountController extends Controller
{
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
