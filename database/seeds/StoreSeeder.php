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
                'name' => 'Plantshed Laravel',
                'tax_id' => 1,
                'created_by' => 1,
            ],
            [
                'company_id' => 2,
                'name' => 'Plantshed Eshop',
                'tax_id' => 1,
                'created_by' => 1,
            ]
        ]);
    }
}
