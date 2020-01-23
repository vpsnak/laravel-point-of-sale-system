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
                'name' => 'PlantShed New York Flowers',
                'phone' => '(212)662-4400',
                'street' => '209 w 96th st',
                'postal_code' => '10025',
                'city' => 'New York NY',
                'tax_id' => 1,
                'created_by' => 1,
            ],
            [
                'company_id' => 2,
                'name' => 'PlantShed Cafe at 87th Street',
                'phone' => '212-662-4400',
                'street' => '555 Columbus Ave',
                'postal_code' => '10024',
                'city' => 'New York NY',
                'tax_id' => 1,
                'created_by' => 1,
            ],
            [
                'company_id' => 2,
                'name' => 'PlantShed Cafe at Prince Street',
                'phone' => '212-662-4400',
                'street' => '1 Prince St',
                'postal_code' => '10012',
                'city' => 'New York NY',
                'tax_id' => 1,
                'created_by' => 1,
            ]
        ]);
    }
}
