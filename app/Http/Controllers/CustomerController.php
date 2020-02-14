<?php

namespace App\Http\Controllers;

use App\Address;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function get(Customer $model)
    {
        return response($model->load('addresses'));
    }

    public function update(Request $request, Customer $model)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:users,id',
            'email' => 'email|unique:users,email,' . $model->id,
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'house_account_status' => 'nullable|bool',
            'house_account_number' => 'nullable|string',
            'house_account_limit' => 'nullable|numeric',
            'no_tax' => 'nullable|bool',
            'file' => 'nullable|file|max:15000|mimes:jpeg,jpg,png,pdf',
            'comment' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        if ($validatedData['no_tax'] && empty($validatedData['file'])) {

            if (empty($model->no_tax_file)) {
                return response(['errors' => ['Zero Tax' => 'Certification file is required when zero tax is enabled']], 422);
            }
        }
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:customers,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'house_account_status' => 'nullable|bool',
            'house_account_number' => 'nullable|string|unique:customers,house_account_number',
            'house_account_limit' => 'nullable|numeric',
            'no_tax' => 'nullable|bool',
            'file' => 'nullable|required_if:no_tax,1|file|max:15000|mimes:jpeg,jpg,png,pdf',
            'comment' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        $addressData = $request->validate([
            'address.first_name' => 'required|string',
            'address.last_name' => 'required|string',
            'address.street' => 'required|string',
            'address.street2' => 'nullable|string',
            'address.city' => 'required|string',
            'address.country_id' => 'required|exists:countries,country_id',
            'address.region' => 'required|exists:regions,region_id',
            'address.postcode' => 'required|string',
            'address.phone' => 'required|string',
            'address.company' => 'nullable|string',
            'address.vat_id' => 'nullable|string',
            'address.is_default_billing' => 'nullable|bool',
            'address.is_default_shipping' => 'nullable|bool',
            'address.location' => 'nullable|string',
            'address.location_name' => 'nullable|string'
        ]);

        $customer = Customer::create($validatedData);

        if (array_key_exists('file', $validatedData) && $validatedData['no_tax']) {
            $filePath = self::noTaxFileCreation($validatedData['file'], $customer->id);
            // @TODO DEBUG
            var_dump($filePath);
            die;
            // $customer->no_tax_file = "/storage/uploads/no_tax/{$customer->id}{$fileExt}";
            $customer->save();
        }

        $addressData['address']['customer_id'] = $customer->id;
        Address::create($addressData['address']);

        return response(Customer::with('addresses')->find($customer->id), 201);
    }

    private static function noTaxFileCreation($file, int $id)
    {
        $fileExt = '.' . $file->extension();
        $filePath = $file->storeAs(
            'public/uploads/no_tax',
            $id . $fileExt
        );

        return $filePath;
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required'
        ]);

        $columns = [
            'first_name',
            'last_name',
            'email',
            'addresses.phone',
            DB::raw("concat(first_name, ' ', last_name)"),
            DB::raw("concat(last_name, ' ', first_name)"),
        ];

        return Customer::query()->search($columns, $validatedData['keyword'])->with('addresses')->get();
    }
}
