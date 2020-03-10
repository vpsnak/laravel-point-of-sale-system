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
            'customer_id' => 1,
            'user_id' => 33,
            'store_id' => 1,
            'status' => 'pending',
            'discount_type' => '',
            'discount_amount' => 0,
            'products' =>
            array(
                0 =>
                array(
                    'id' => 6,
                    'magento_id' => 0,
                    'stock_id' => 0,
                    'sku' => 'c9a85fc1-f303-3320-a016-0c5c19fd12a9',
                    'name' => 'Blue Diamonds',
                    'barcode' => null,
                    'photo_url' => 'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10223_sienna.jpg',
                    'url' => null,
                    'plantcare_pdf' => null,
                    'description' => null,
                    'deleted_at' => null,
                    'created_at' => '2019-11-13 14:15:35',
                    'updated_at' => '2019-11-13 14:15:35',
                    'final_price' => '63.00',
                    'stock' => -96,
                    'magento_stock' => null,
                    'laravel_stock' => -96,
                    'stores' =>
                    array(
                        0 =>
                        array(
                            'id' => 1,
                            'name' => 'Plantshed Laravel',
                            'tax_id' => 1,
                            'user_id' => 1,
                            'created_at' => null,
                            'updated_at' => null,
                            'pivot' =>
                            array(
                                'product_id' => 6,
                                'store_id' => 1,
                                'qty' => -96,
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
                        'id' => 6,
                        'amount' => '63.0000',
                        'discount_id' => null,
                        'priceable_id' => 6,
                        'priceable_type' => 'App\\Product',
                        'created_at' => '2019-11-13 14:15:35',
                        'updated_at' => '2019-11-13 14:15:35',
                        'discount' => null,
                    ),
                    'categories' =>
                    array(
                        0 =>
                        array(
                            'id' => 10,
                            'name' => 'East Russell',
                            'is_enabled' => true,
                            'created_at' => '2019-11-13 14:15:36',
                            'updated_at' => '2019-11-13 14:15:36',
                            'pivot' =>
                            array(
                                'product_id' => 6,
                                'category_id' => 10,
                            ),
                        ),
                    ),
                    'qty' => 1,
                ),
            ),
            'shipping' =>
            array(
                'notes' => 'sfdafasf',
                'address' =>
                array(
                    'id' => 1,
                    'first_name' => 'Darron',
                    'last_name' => 'Huels',
                    'street' => '18136 Hermiston Shoals',
                    'street2' => NULL,
                    'city' => 'New Emmett',
                    'country_id' => 'GL',
                    'region' => 1,
                    'postcode' => '01524',
                    'phone' => '34234234234',
                    'company' => 'dsfdsf',
                    'vat_id' => 'dfssdfsdf',
                    'billing' => 0,
                    'shipping' => 0,
                    'created_at' => '2019-11-13 14:15:34',
                    'updated_at' => '2019-11-13 14:28:23',
                    'address_country' => 'GL',
                    'address_region' =>
                    array(
                        'region_id' => 1,
                        'country_id' => 'US',
                        'code' => 'AL',
                        'default_name' => 'Alabama',
                    ),
                    'pivot' =>
                    array(
                        'customer_id' => 1,
                        'address_id' => 1,
                    ),
                    'country' =>
                    array(
                        'country_id' => 0,
                        'iso2_code' => 'GL',
                        'iso3_code' => 'GRL',
                    ),
                    'region_id' =>
                    array(
                        'region_id' => 1,
                        'country_id' => 'US',
                        'code' => 'AL',
                        'default_name' => 'Alabama',
                    ),
                ),
                'method' => 'delivery',
                'date' => '2019-11-14',
                'timeSlotLabel' => '17:00-20:00',
                'cost' => '12',
                'location' => 'Business',
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
        var_dump(json_decode($response));
    }
}
