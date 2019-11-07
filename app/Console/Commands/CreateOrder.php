<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class CreateOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:create:order';

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
        $order_data = array(
            'customer_id' => 3,
            'created_by' => 33,
            'store_id' => 1,
            'status' => 'pending',
            'discount_type' => '',
            'discount_amount' => 0,
            'products' =>
                array(
                    0 =>
                        array(
                            'id' => 2,
                            'magento_id' => 0,
                            'stock_id' => 0,
                            'sku' => 'ff56c4e5-0ea7-3d53-a544-bac37af023f9',
                            'name' => 'Friends Forever',
                            'barcode' => null,
                            'photo_url' => 'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10572.jpg',
                            'url' => null,
                            'plantcare_pdf' => null,
                            'description' => null,
                            'deleted_at' => null,
                            'created_at' => '2019-11-07 09:06:37',
                            'updated_at' => '2019-11-07 09:06:37',
                            'final_price' => '94.0000',
                            'stock' => 8,
                            'magento_stock' => null,
                            'laravel_stock' => 8,
                            'stores' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 1,
                                            'name' => 'Plantshed Laravel',
                                            'tax_id' => 1,
                                            'created_by' => 1,
                                            'created_at' => null,
                                            'updated_at' => null,
                                            'pivot' =>
                                                array(
                                                    'product_id' => 2,
                                                    'store_id' => 1,
                                                    'qty' => 8,
                                                ),
                                            'tax' =>
                                                array(
                                                    'id' => 1,
                                                    'name' => 'Zero Tax',
                                                    'percentage' => 0,
                                                    'is_default' => 0,
                                                    'created_at' => null,
                                                    'updated_at' => null,
                                                ),
                                        ),
                                ),
                            'price' =>
                                array(
                                    'id' => 2,
                                    'amount' => '94.0000',
                                    'discount_id' => NULL,
                                    'priceable_id' => 2,
                                    'priceable_type' => 'App\\Product',
                                    'created_at' => '2019-11-07 09:06:37',
                                    'updated_at' => '2019-11-07 09:06:37',
                                    'discount' => NULL,
                                ),
                            'categories' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 2,
                                            'name' => 'Dawsonview',
                                            'in_product_listing' => 1,
                                            'created_at' => '2019-11-07 09:06:38',
                                            'updated_at' => '2019-11-07 09:06:38',
                                            'pivot' =>
                                                array(
                                                    'product_id' => 2,
                                                    'category_id' => 2,
                                                ),
                                        ),
                                    1 =>
                                        array(
                                            'id' => 3,
                                            'name' => 'North Hilton',
                                            'in_product_listing' => 0,
                                            'created_at' => '2019-11-07 09:06:38',
                                            'updated_at' => '2019-11-07 09:06:38',
                                            'pivot' =>
                                                array(
                                                    'product_id' => 2,
                                                    'category_id' => 3,
                                                ),
                                        ),
                                    2 =>
                                        array(
                                            'id' => 9,
                                            'name' => 'Port Keaganfort',
                                            'in_product_listing' => 0,
                                            'created_at' => '2019-11-07 09:06:38',
                                            'updated_at' => '2019-11-07 09:06:38',
                                            'pivot' =>
                                                array(
                                                    'product_id' => 2,
                                                    'category_id' => 9,
                                                ),
                                        ),
                                    3 =>
                                        array(
                                            'id' => 10,
                                            'name' => 'Lake Craigberg',
                                            'in_product_listing' => 1,
                                            'created_at' => '2019-11-07 09:06:38',
                                            'updated_at' => '2019-11-07 09:06:38',
                                            'pivot' =>
                                                array(
                                                    'product_id' => 2,
                                                    'category_id' => 10,
                                                ),
                                        ),
                                ),
                            'qty' => 1,
                            'notes' => 'asdasdasd',
                        ),
                    1 =>
                        array(
                            'id' => 6,
                            'magento_id' => 0,
                            'stock_id' => 0,
                            'sku' => '6faf2bdc-0799-38d3-aef3-493cf8431e26',
                            'name' => 'Limoncello Duo',
                            'barcode' => NULL,
                            'photo_url' => 'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10572.jpg',
                            'url' => NULL,
                            'plantcare_pdf' => NULL,
                            'description' => NULL,
                            'deleted_at' => NULL,
                            'created_at' => '2019-11-07 09:06:37',
                            'updated_at' => '2019-11-07 09:06:37',
                            'final_price' => '28.0000',
                            'stock' => -34,
                            'magento_stock' => NULL,
                            'laravel_stock' => -34,
                            'stores' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 1,
                                            'name' => 'Plantshed Laravel',
                                            'tax_id' => 1,
                                            'created_by' => 1,
                                            'created_at' => NULL,
                                            'updated_at' => NULL,
                                            'pivot' =>
                                                array(
                                                    'product_id' => 6,
                                                    'store_id' => 1,
                                                    'qty' => -34,
                                                ),
                                            'tax' =>
                                                array(
                                                    'id' => 1,
                                                    'name' => 'Zero Tax',
                                                    'percentage' => 0,
                                                    'is_default' => 0,
                                                    'created_at' => NULL,
                                                    'updated_at' => NULL,
                                                ),
                                        ),
                                ),
                            'price' =>
                                array(
                                    'id' => 6,
                                    'amount' => '28.0000',
                                    'discount_id' => NULL,
                                    'priceable_id' => 6,
                                    'priceable_type' => 'App\\Product',
                                    'created_at' => '2019-11-07 09:06:37',
                                    'updated_at' => '2019-11-07 09:06:37',
                                    'discount' => NULL,
                                ),
                            'categories' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 6,
                                            'name' => 'Port Geovanni',
                                            'in_product_listing' => 0,
                                            'created_at' => '2019-11-07 09:06:38',
                                            'updated_at' => '2019-11-07 09:06:38',
                                            'pivot' =>
                                                array(
                                                    'product_id' => 6,
                                                    'category_id' => 6,
                                                ),
                                        ),
                                    1 =>
                                        array(
                                            'id' => 9,
                                            'name' => 'Port Keaganfort',
                                            'in_product_listing' => 0,
                                            'created_at' => '2019-11-07 09:06:38',
                                            'updated_at' => '2019-11-07 09:06:38',
                                            'pivot' =>
                                                array(
                                                    'product_id' => 6,
                                                    'category_id' => 9,
                                                ),
                                        ),
                                ),
                            'qty' => 1,
                            'notes' => 'asdasd',
                        ),
                ),
            'shipping' =>
                array(
                    'notes' => 'asdasdasd',
                    'address' =>
                        array(
                            'id' => 5,
                            'first_name' => 'Shemar',
                            'last_name' => 'Eichmann',
                            'street' => '233 Cronin Mills',
                            'street2' => NULL,
                            'city' => 'East Abdullah',
                            'country_id' => 'TH',
                            'region' => 'Saint Helena',
                            'postcode' => '01524',
                            'phone' => '1-747-320-9878',
                            'company' => NULL,
                            'vat_id' => NULL,
                            'billing' => 0,
                            'shipping' => 0,
                            'created_at' => '2019-11-07 09:06:36',
                            'updated_at' => '2019-11-07 09:06:36',
                            'address_country' => 'TH',
                            'address_region' => 'Saint Helena',
                            'pivot' =>
                                array(
                                    'customer_id' => 3,
                                    'address_id' => 5,
                                ),
                            'country' =>
                                array(
                                    'country_id' => 0,
                                    'iso2_code' => 'TH',
                                    'iso3_code' => 'THA',
                                ),
                            'region_id' => NULL,
                            'region_name' => NULL,
                        ),
                    'method' => 'delivery',
                    'date' => '2019-11-20',
                    'timeSlotLabel' => '17:00-20:00',
                    'timeSlotCost' => '30',
                    'location' => 'Hospital',
                    'occasion' => 'Birthday',
                ),
        );

        $create_order = (new Client())
            ->post('http://plantshed.local/api/orders/create', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'connect_timeout' => 30,
                'body' => json_encode($order_data)
            ]);

        $response = $create_order->getBody()->getContents();
        var_dump($response);
    }
}
