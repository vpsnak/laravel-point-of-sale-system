<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;
use App\Region;
use App\StorePickup;

class AddressController extends Controller
{
    private $address;

    public function all()
    {
        return response(Address::paginate());
    }

    public function getOne(Address $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $this->address = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'first_name' => 'nullable|string|required_with:customer_id',
            'last_name' => 'nullable|string|required_with:customer_id',
            'street' => 'required|string',
            'street2' => 'nullable|string',
            'city' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'required|exists:regions,id',
            'postcode' => 'required|string',
            'phone' => 'required|string',
            'company' => 'nullable|string',
            'vat_id' => 'nullable|string',
            'is_default_billing' => 'nullable|bool',
            'is_default_shipping' => 'nullable|bool',
            'location' => 'nullable|numeric|between:1,12',
            'location_name' => 'nullable|string'
        ]);
        $response = self::regionValidation($this->address['region_id'], $this->address['country_id']);

        if (isset($response['errors'])) {
            return response($response, 422);
        } else {
            unset($this->address['country_id']);

            $address = Address::create($this->address);

            return response(['address' => $address->load('region'), 'notification' => [
                'msg' => ["Address successfully created!"],
                'type' => 'success'
            ]], 201);
        }
    }

    public function update(Request $request)
    {
        $this->address = $request->validate([
            'id' => 'required|exists:addresses,id',
            'first_name' => 'nullable|string|required_with:customer_id',
            'last_name' => 'nullable|string|required_with:customer_id',
            'region_id' => 'required|exists:regions,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'street' => 'required|string',
            'street2' => 'nullable|string',
            'city' => 'required|string',
            'postcode' => 'required|string',
            'phone' => 'required|numeric',
            'company' => 'nullable|string',
            'vat_id' => 'nullable|string',
            'is_default_billing' => 'nullable|bool',
            'is_default_shipping' => 'nullable|bool',
            'location' => 'nullable|numeric|between:1,12',
            'location_name' => 'nullable|string'
        ]);
        $response = self::regionValidation($this->address['region_id'], $this->address['country_id']);
        if (isset($response['errors'])) {
            return response($response, 422);
        } else {
            unset($this->address['country_id']);

            $address = Address::findOrFail($this->address['id']);
            $address->fill($this->address);
            $address->save();

            return response([
                'address' => $address->load('region'),
                'notification' => [
                    'msg' => ['Address successfully updated!'],
                    'type' => 'success'
                ]
            ]);
        }
    }

    private static function regionValidation(int $region_id, int $country_id)
    {
        $region = Region::findOrFail($region_id);
        if ($region->country->id !== $country_id) {
            return ['errors' => ["The selected region/state does not match the selected country"]];
        } else {
            return [];
        }
    }
}
