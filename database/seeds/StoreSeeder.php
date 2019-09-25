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
                'name' => 'Plantshed Central',
                'taxable' => 1,
                'is_default' => 1,
                'tax_id' => 1,
                'created_by' => 1,
            ]
        ]);
        DB::table('stores')->insert([
            [
                'name' => 'Plantshed Eshop',
                'taxable' => 1,
                'is_default' => 1,
                'tax_id' => 1,
                'created_by' => 1,
            ]
        ]);
    }
}
