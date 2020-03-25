<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stores')->insert([
            [
                'company_id' => 1,
                'name' => 'Plantshed store - 96th st',
                'phone' => '212-662-4400',
                'street' => '209 W 96th st',
                'postcode' => '10025',
                'city' => 'New York',
                'active' => true,
                'is_phone_center' => false,
                'default_currency' => 'USD',
                'tax_id' => 1,
                'created_by_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_id' => 2,
                'name' => 'Plantshed store - 87th st',
                'phone' => '212-662-4400',
                'street' => '555 Columbus Ave',
                'postcode' => '10024',
                'city' => 'New York',
                'active' => true,
                'is_phone_center' => false,
                'default_currency' => 'USD',
                'tax_id' => 1,
                'created_by_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_id' => 2,
                'name' => 'Plantshed customer service',
                'phone' => '212-662-4400',
                'street' => '96th st',
                'postcode' => '10025',
                'city' => 'New York',
                'active' => true,
                'is_phone_center' => true,
                'default_currency' => 'USD',
                'tax_id' => 1,
                'created_by_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_id' => 3,
                'name' => 'Plantshed store - Prince st',
                'phone' => '212-662-4400',
                'street' => '1 Prince st',
                'postcode' => '10012',
                'city' => 'New York',
                'active' => true,
                'is_phone_center' => false,
                'default_currency' => 'USD',
                'tax_id' => 1,
                'created_by_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // store using demo elavon acc
            [
                'company_id' => 4,
                'name' => 'Demo elavon',
                'phone' => '333-777-1312',
                'street' => 'demo skinika',
                'postcode' => '667',
                'city' => 'New York',
                'active' => true,
                'is_phone_center' => false,
                'default_currency' => 'USD',
                'tax_id' => 1,
                'created_by_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
