<?php

namespace App\Http\Controllers;

use App\MasAccount;
use App\Region;
use Illuminate\Http\Request;

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

    public function getDefaultRecipientAccount($environment)
    {
        return response(MasAccount::where('environment', $environment)->first('default_recipient_account'));
    }

    public function getDefaultOnlinePartnerAccount($environment)
    {
        return response(MasAccount::where('environment', $environment)->first('default_online_partner_account'));
    }

    public function setDefaultRecipientAccount(Request $request)
    {
        $validatedData = $request->validate([
            'environment' => 'required|exists:mas_accounts,environment',
            'region_id' => 'required|exists:regions,id',

            'ExtensionData' => 'nullable|string',
            'RecipientFirstName' => 'required|string',
            'RecipientLastName' => 'required|string',
            'RecipientEmail' => 'required|email',
            'RecipientAddress' => 'required|string',
            'RecipientAddress2' => 'nullable|string',
            'RecipientCity' => 'required|string',
            'RecipientZip' => 'required|numeric|digits:5',
            'RecipientPhone' => 'required|string',
            'CardMessage' => 'nullable|string',
            'OccasionType' => 'required|numeric',
            'ResidenceType' => 'required|numeric',
        ]);
        $mas_account = MasAccount::where('environment', $validatedData['environment'])->first();
        $region = Region::findOrFail($validatedData['region_id']);


        unset($validatedData['region_id']);
        unset($validatedData['environment']);
        $validatedData['RecipientState'] = $region->code;
        $validatedData['RecipientCountry'] = $region->country->iso2_code;
        $mas_account->default_recipient_account = $validatedData;
        $mas_account->save();

        return response([
            'notification' => [
                'msg' => ["Default recipient account for mas env: {$mas_account->environment} updated successfully!"],
                'type' => 'success'
            ]
        ]);
    }

    public function setDefaultOnlinePartnerAccount(Request $request)
    {
        $validatedData = $request->validate([
            'environment' => 'required|exists:mas_accounts,environment',
            'region_id' => 'required|exists:regions,id',

            'SoldName' => 'required|string',
            'SoldAddress' => 'required|string',
            'SoldAddress2' => 'nullable|string',
            'SoldCity' => 'required|string',
            'SoldZip' => 'required|string|size:5',
            'SoldPhoneHome' => 'required|string',
            'SoldPhoneWork' => 'required|string',
            'SoldPhoneMobile' => 'required|string',
            'SoldEmail' => 'required|email',
            'SalesRep' => 'nullable|string',
            'ShippingVia' => 'nullable|string',
            'ShippingPriority' => 'nullable|numeric',
            'AuthCode' => 'nullable|string',
            'SoldAttention' => 'nullable|string',
            'CustomerId' => 'nullable|string',
        ]);
        $mas_account = MasAccount::where('environment', $validatedData['environment'])->first();
        $region = Region::findOrFail($validatedData['region_id']);

        unset($validatedData['region_id']);
        unset($validatedData['environment']);
        $validatedData['SoldState'] = $region->code;
        // $validatedData['SoldCountry'] = $region->country->iso2_code;
        $mas_account->default_online_partner_account = $validatedData;
        $mas_account->save();

        return response([
            'notification' => [
                'msg' => ["Default online partnet account for mas env: {$mas_account->environment} updated successfully!"],
                'type' => 'success'
            ]
        ]);
    }
}
