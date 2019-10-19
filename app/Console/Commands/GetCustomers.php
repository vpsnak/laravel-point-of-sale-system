<?php

namespace App\Console\Commands;

use App\Address;
use App\Http\Controllers\Magento\Customer;
use App\Http\Controllers\Magento\Helper;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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

        $page = 1;
        $per_page = 100;
        $categoriesRetrieved = $per_page;
        while ($categoriesRetrieved == $per_page) {
            $customers = $client->getAllEntries($per_page, $page++);
            if (empty($customers)) {
                break;
            }
            foreach ($customers as $customer) {
                $fieldsToParse = [
                    'entity_id',
                    'firstname',
                    'lastname',
                ];
                $fieldsToRename = [
                    'entity_id' => 'magento_id',
                    'firstname' => 'first_name',
                    'lastname' => 'last_name',
                ];
                $parsedCustomer = Helper::getParsedData($customer, $fieldsToParse, $fieldsToRename);
                $storedCustomer = \App\Customer::getFirst('email', $customer->email);
                if (Helper::hasDifferences($parsedCustomer, $storedCustomer)) {
                    $logMessage = 'Customer: ' . $customer->email;
                    $this->info($logMessage);
                    $storedCustomer = \App\Customer::updateOrCreate(
                        ['email' => $customer->email],
                        $parsedCustomer
                    );
                }
                foreach ($customer->addresses as $address) {
                    $fieldsToParse = [
                        'firstname',
                        'lastname',
                        'street',
                        'city',
                        'country_id',
                        'region_id',
                        'postcode',
                        'telephone',
                        'company',
                        'vat_id',
                    ];
                    $fieldsToRename = [
                        'firstname' => 'first_name',
                        'lastname' => 'last_name',
                        'region_id' => 'region',
                        'telephone' => 'phone',
                    ];
                    try {
                        $parsedAddress = Helper::getParsedData($address, $fieldsToParse, $fieldsToRename);
                        $parsedAddress['area_code_id'] = 20;
                        $storedAddress = Address::getFirst('magento_id', $address->entity_id);
                        if (Helper::hasDifferences($parsedAddress, $storedAddress)) {
                            $logMessage = 'Address: ' . $address->entity_id;
                            $this->info($logMessage);
                            $updatedAddress = Address::updateOrCreate(
                                ['magento_id' => $address->entity_id],
                                $parsedAddress
                            );
                            if (!empty($storedAddress)) {
                                $storedCustomer->addresses()->attach($updatedAddress);
                            }
                        }
                    } catch (Exception $e) {
                        $logMessage = 'skipping Customer ' . $customer->email . ' Address: ' . $address->entity_id;
                        $this->info($logMessage);
                        Log::debug($logMessage);
                        continue;
                    }
                }
            }
        }
    }
}
