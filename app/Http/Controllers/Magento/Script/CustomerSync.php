<?php


namespace App\Http\Controllers\Magento\Script;


use App\Address;
use App\Http\Controllers\Magento\Customer;
use App\Http\Controllers\Magento\Helper;
use Illuminate\Support\Facades\Log;

class CustomerSync
{
    const LOG_PREFIX = 'Customer Sync';

    protected const customerFieldsToParse = [
        'entity_id',
        'firstname',
        'lastname',
    ];

    protected const customerFieldsToRename = [
        'entity_id' => 'magento_id',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
    ];

    protected const addressFieldsToParse = [
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
        'is_default_billing',
        'is_default_shipping'
    ];
    protected const addressFieldsToRename = [
        'firstname' => 'first_name',
        'lastname' => 'last_name',
        'telephone' => 'phone'
    ];

    public static function getFromMagento($force = false)
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
                if (count($customer->addresses) === 0) {
                    continue;
                }
                $parsedCustomer = Helper::getParsedData(
                    $customer,
                    self::customerFieldsToParse,
                    self::customerFieldsToRename
                );
                $storedCustomer = \App\Customer::getFirst('email', $customer->email);
                if ($force || Helper::hasDifferences($parsedCustomer, $storedCustomer)) {
                    self::log('Getting Customer: ' . $customer->email);
                    $storedCustomer = \App\Customer::updateOrCreate(
                        ['email' => $customer->email],
                        $parsedCustomer
                    );
                }
                foreach ($customer->addresses as $address) {
                    try {
                        $parsedAddress = Helper::getParsedData(
                            $address,
                            self::addressFieldsToParse,
                            self::addressFieldsToRename
                        );
                        $parsedAddress['customer_id'] = $storedCustomer->id;
                        echo $parsedAddress['region_id'];

                        $storedAddress = Address::getFirst('magento_id', $address->entity_id);
                        if ($force || Helper::hasDifferences($parsedAddress, $storedAddress)) {
                            self::log('Getting Customer (' . $customer->email . ') Address: ' . $address->entity_id);

                            // check if magento address has 2 streets and parse them
                            $street = explode("\n", $address->street);
                            if (count($street) > 1) {
                                $parsedAddress['street'] = $street[0];
                                $parsedAddress['street2'] = $street[1];
                            }
                            self::log($storedCustomer->id);

                            $updatedAddress = Address::updateOrCreate(
                                ['magento_id' => $address->entity_id],
                                $parsedAddress
                            );
                        }
                    } catch (\Exception $e) {
                        self::log($e);
                        // self::log('Skipping Customer (' . $customer->email . ') Address: ' . $address->entity_id);
                        continue;
                    }
                }
            }
        }
    }

    private static function log($message)
    {
        Log::channel('connector')->info(self::LOG_PREFIX . ': ' . $message);
    }

    public static function sendToMagento()
    {
        $client = new Customer();
        foreach (\App\Customer::all() as $customer) {
            if ($customer->magento_id == 0) {
                //                // @TODO add tax vat to customer
                $response = $client->sendCustomer([
                    'firstname' => $customer->first_name,
                    'lastname' => $customer->last_name,
                    'email' => $customer->email,
                ]);
                if (!isset($response->id)) {
                    return;
                }
                $logMessage = 'Send Customer (' . $customer->email . ') with magento id: ' . $response->id;
                self::log($logMessage);
                $customer->magento_id = $response->id;
                $customer->save();
            }
            // @TODO set 1st addresss default billing and shipping in there is none
            foreach ($customer->addresses as $address) {
                if ($address->magento_id == 0) {
                    $response = $client->sendAddress($customer->magento_id, [
                        'address_code' => $address->id,
                        'firstname' => $address->first_name,
                        'lastname' => $address->last_name,
                        'company' => $address->company,
                        'street' => [
                            $address->street,
                            $address->street2
                        ],
                        'city' => $address->city,
                        'country_id' => $address->country_id,
                        'region' => $address->region,
                        'postcode' => $address->postcode,
                        'phone' => $address->phone,
                        'vat_id' => $address->vat_id,
                        'is_default_billing' => $address->billing,
                        'is_default_shipping' => $address->shipping
                    ]);
                    if (!isset($response->id)) {
                        continue;
                    }
                    $logMessage = 'Send Customer (' . $customer->email . ') Address (' . $address->id . ') with magento id: ' . $response->id;
                    self::log($logMessage);
                    $address->magento_id = $response->id;
                    $address->save();
                }
            }
        }
    }
}
