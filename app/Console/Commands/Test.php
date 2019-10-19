<?php

namespace App\Console\Commands;

use App\Http\Controllers\Magento\Customer;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // oob
        $callback_url = 'https://httpbin.org/get';
        $client = new Customer();
        dd($client->getAllEntries(1, 1));
        foreach (\App\Customer::all() as $customer) {
            $res = $client->sendCustomer([
                'firstname' => $customer->first_name,
                'lastname' => $customer->last_name,
                'email' => $customer->email,
                'taxvat' => '151581515',
            ]);
            if (!isset($res->id)) {
                return;
            }
            echo 'Customer send with magento id: ' . $res->id . PHP_EOL;
            $customer->magento_id = $res->id;
            $customer->save();
            foreach ($customer->addresses as $address) {
                $res = $client->sendAddress($customer->magento_id, [
                    'address_code' => $address->id,
                    'firstname' => $address->first_name,
                    'lastname' => $address->last_name,
                    'company' => 'webo2',
                    'street' => [$address->street],
                    'city' => $address->city,
                    'country_id' => $address->country_id,
                    'region' => $address->region,
                    'postcode' => $address->postcode,
                    'phone' => $address->phone,
                    'vat_id' => '151581515',
                    'is_default_billing' => 1,
                    'is_default_shipping' => 1
                ]);
                if (!isset($res->id)) {
                    continue;
                }
                echo 'Address send with magento id: ' . $res->id . PHP_EOL;
                $address->magento_id = $res->id;
                $address->save();
            }
        }
    }
}
