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
                'address_id' => factory(App\Address::class)->create()->id
            ]
        ]);
        DB::table('stores')->insert([
            [
                'name' => 'Plantshed Eshop',
                'address_id' => factory(App\Address::class)->create()->id
            ]
        ]);
    }
}
