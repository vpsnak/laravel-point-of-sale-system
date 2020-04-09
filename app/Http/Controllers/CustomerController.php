<?php

namespace App\Http\Controllers;

use App\Address;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function all()
    {
        return response(Customer::paginate(10));
    }

    public function getOne(int $model)
    {
        return response(Customer::with(['addresses', 'notes'])->findOrFail($model));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:customers,id',
            'email' => "email|unique:customers,email,{$request->id}",
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'house_account_status' => 'nullable|bool',
            'house_account_number' => "nullable|unique:customers,house_account_number,{$request->id}",
            'house_account_limit_price' => 'nullable|array',
            'no_tax' => 'nullable|bool',
            'file' => 'nullable|file|max:15000|mimes:jpeg,jpg,png,pdf',
            'phone' => "nullable|string|unique:customers,phone,{$request->id}",
        ]);

        $customer = Customer::findOrFail($validatedData['id']);

        if (isset($validatedData['no_tax']) && $validatedData['no_tax'] && empty($validatedData['file'])) {

            if (empty($customer->no_tax_file)) {
                return response(['errors' => ['Certification file is required when zero tax is enabled']], 422);
            }
        }

        $customer->update($validatedData);

        return response([
            'customer' => $customer,
            'notification' => [
                'msg' => ["Customer {$customer->name} updated successfully"],
                'type' => 'success'
            ]
        ]);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:customers,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'house_account_status' => 'nullable|bool',
            'house_account_number' => 'nullable|string|unique:customers,house_account_number',
            'house_account_limit_price' => 'nullable|numeric',
            'no_tax' => 'nullable|bool',
            'file' => 'nullable|required_if:no_tax,1|file|max:15000|mimes:jpeg,jpg,png,pdf',
            'phone' => 'nullable|string|unique:customers,phone',
        ]);

        $customer = Customer::create($validatedData);

        if (array_key_exists('file', $validatedData) && $validatedData['no_tax']) {
            $filePath = self::noTaxFileCreation($validatedData['file'], $customer->id);
            // @TODO DEBUG
            // var_dump($filePath);
            // die;
            // $customer->no_tax_file = "/storage/uploads/no_tax/{$customer->id}{$fileExt}";
            $customer->no_tax_file = $filePath;
            $customer->save();
        }

        return response([
            'customer' => $customer,
            'notification' => [
                'msg' => ["Customer {$customer->name} created successfully"],
                'type' => 'success'
            ]
        ], 201);
    }

    private static function noTaxFileCreation($file, int $id)
    {
        $fileExt = ".{$file->extension()}";
        $filePath = $file->storeAs(
            'public/uploads/no_tax',
            "{$id}{$fileExt}"
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
