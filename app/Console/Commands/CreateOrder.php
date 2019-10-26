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
            'customer_id' => 2,
            'created_by' => 1,
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
                            'sku' => '7376f94e-0072-3155-a8c9-24497e1b5576',
                            'name' => 'Friends Forever',
                            'barcode' => null,
                            'photo_url' => 'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10555_make_me_blush.jpg',
                            'url' => null,
                            'description' => null,
                            'deleted_at' => null,
                            'created_at' => '2019-10-26 14:26:57',
                            'updated_at' => '2019-10-26 14:26:57',
                            'final_price' => '57.0000',
                            'stock' => 4,
                            'magento_stock' => null,
                            'laravel_stock' => 4,
                            'price' =>
                                array(
                                    'id' => 9,
                                    'amount' => '57.0000',
                                    'discount_id' => null,
                                    'priceable_id' => 6,
                                    'priceable_type' => 'App\\Product',
                                    'created_at' => '2019-10-26 14:26:57',
                                    'updated_at' => '2019-10-26 14:26:57',
                                ),
                            'qty' => 1,
                        ),
                ),
            'shipping' =>
                array(
                    'notes' => 'comment',
                    'address' =>
                        array(
                            'id' => 4,
                            'first_name' => 'Oceane',
                            'last_name' => 'Stiedemann',
                            'street' => '74105 Fay Wells Apt. 880',
                            'street2' => null,
                            'city' => 'Bahringerton',
                            'country_id' => 'SE',
                            'region' => 'Saint Martin',
                            'postcode' => '01561',
                            'phone' => '1-705-738-9431 x865',
                            'company' => null,
                            'vat_id' => null,
                            'deliverydate' => null,
                            'created_at' => '2019-10-26 14:26:57',
                            'updated_at' => '2019-10-26 14:26:57',
                            'address_country' => 'SE',
                            'address_region' => null,
                            'country' =>
                                array(
                                    'country_id' => 0,
                                    'iso2_code' => 'SE',
                                    'iso3_code' => 'SWE',
                                ),
                            'region_id' => null,
                            'region_name' => null,
                        ),
                    'method' => 'delivery',
                    'date' => '2019-10-29',
                    'timeSlotLabel' => '01:00-02:00',
                    'timeSlotCost' => '23',
                ),
        );

        $create_order = (new Client())
            ->post('http://localhost:8000/api/orders/create', [
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
