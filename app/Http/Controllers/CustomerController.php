<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CustomerController extends BaseController
{
    protected $model = Customer::class;

    public function create(Request $request)
    {
        $validatedExtra = $request->validate([
            'id' => 'nullable|exists:customers,id',
            'address' => 'nullable|array',
        ]);

        if (!empty($validatedExtra['id'])) {
            $validatedData = $request->validate([
                'email' => [
                    'required',
                    'email',
                    Rule::unique('customers', 'email')->ignore($validatedExtra['id'])
                ],
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'house_account_status' => 'nullable|bool',
                'house_account_number' => 'nullable|string',
                'house_account_limit' => 'nullable|numeric',
                'no_tax' => 'nullable|bool',
                'file' => 'nullable|required_if:no_tax,1|file|mimes:jpeg,jpg,png,pdf',
                'comment' => 'nullable|string',
            ]);
        } else {
            $validatedData = $request->validate([
                'email' => 'required|email|unique:customers,email',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'house_account_status' => 'nullable|bool',
                'house_account_number' => 'nullable|string|unique:customers,house_account_number',
                'house_account_limit' => 'nullable|numeric',
                'no_tax' => 'nullable|bool',
                'file' => 'nullable|required_if:no_tax,1|file|mimes:jpeg,jpg,png,pdf',
                'comment' => 'nullable|string',
            ]);
        }

        $timestamp = idate("U");

        if (array_key_exists('file', $validatedData) && $validatedData['no_tax']) {
            if ($validatedExtra['id']) {
                $validatedData['no_tax_file'] = $request->file('file')->storeAs(
                    'public/uploads/no_tax',
                    $validatedExtra['id'] . '.' . $request->file('file')->extension()
                );
            } else {
                $validatedData['no_tax_file'] = $request->file('file')->storeAs(
                    'public/uploads/no_tax',
                    $timestamp . '.' . $request->file('file')->extension()
                );
            }

            unset($validatedData['file']);
        }

        $customer = $this->getCustomer($validatedExtra['id'] ?? null, $validatedData);

        if (!$validatedExtra['id'] && array_key_exists('file', $validatedData) && $validatedData['no_tax']) {
            Storage::move('public/uploads/no_tax/' . $timestamp, 'public/uploads/no_tax/' . $customer->id . '.' . $request->file('file')->extension());
        }

        $customer->no_tax_file = '/storage/uploads/no_tax/' . $customer->id . '.' . $request->file('file')->extension();
        $customer->save();

        if (!empty($validatedExtra['address'])) {
            $addressData = $request->validate([
                'address.first_name' => 'required|string',
                'address.last_name' => 'required|string',
                'address.street' => 'required|string',
                'address.street2' => 'nullable|string',
                'address.city' => 'required|string',
                'address.country_id' => 'required|exists:countries,country_id',
                'address.region' => 'required|exists:regions,region_id',
                'address.postcode' => 'required|string',
                'address.phone' => 'required|numeric',
                'address.company' => 'nullable|string',
                'address.vat_id' => 'nullable|string',
                'address.billing' => 'nullable|bool',
                'address.shipping' => 'nullable|bool',
            ]);

            $customer->addresses()->create($addressData['address']);
        }

        // @TODO fix update
        return response($customer, empty($validatedExtra['id']) ? 201 : 200);
    }

    private function getCustomer($id, $data)
    {
        if (!empty($id)) {
            $this->model::updateData($id, $data);
            $model = $this->model::getOne($id);
        } else {
            $model = $this->model::store($data);
        }
        return $model;
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required'
        ]);

        return $this->searchResult(
            [
                'first_name',
                'last_name',
                'email',
                'addresses.phone',
                DB::raw("concat(first_name, ' ', last_name)"),
                DB::raw("concat(last_name, ' ', first_name)"),
            ],
            $validatedData['keyword']
        );
    }
}
