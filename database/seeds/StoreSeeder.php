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
                'name' => 'Plantshed Laravel',
                'tax_id' => 1,
                'created_by' => 1,
            ]
        ]);
        DB::table('stores')->insert([
            [
                'name' => 'Plantshed Eshop',
                'tax_id' => 1,
                'created_by' => 1,
            ]
        ]);
    }
}
