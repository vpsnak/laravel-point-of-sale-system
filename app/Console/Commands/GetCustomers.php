<?php

namespace App\Console\Commands;

use App\Address;
use App\Http\Controllers\Magento\Customer;
use Illuminate\Console\Command;

class GetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:getcustomers';

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
        $client = new Customer();

        $page = 4;
        $per_page = 1;
        $categoriesRetrieved = $per_page;
        while ($categoriesRetrieved == $per_page) {
            $customers = $client->getAllEntries($per_page, $page++);
            if (empty($customers)) {
                break;
            }
            foreach ($customers as $customer) {
                $logMessage = 'Customer: ' . $customer->email;
                $this->info($logMessage);
                $storedCustomer = \App\Customer::updateOrCreate(
                    ['email' => $customer->email],
                    [
                        'magento_id' => $customer->entity_id,
                        'first_name' => $customer->firstname,
                        'last_name' => $customer->lastname,
                    ]
                );
                foreach ($customer->addresses as $address) {
                    $logMessage = 'Address: ' . $address->entity_id;
                    $this->info($logMessage);
                    $storedAddress = Address::updateOrCreate(
                        ['magento_id' => $address->entity_id],
                        [
                            'area_code_id' => 20,
                            'first_name' => $address->firstname,
                            'last_name' => $address->lastname,
                            'street' => $address->street,
                            'city' => $address->city,
                            'country_id' => $address->country_id,
                            'region' => $address->region_id,
                            'postcode' => $address->postcode,
                            'phone' => $address->phone,
                            'company' => $address->phone,
                            'vat_id' => $address->phone
                        ]
                    );
                    $storedCustomer->attach($storedAddress);
                }
            }
            break;
        }
    }
}
