<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_types')->insert([
            ['name' => 'Cash', 'type' => 'cash', 'status' => 1, 'is_default' => 1, 'created_by' => 1],
            ['name' => 'Credit', 'type' => 'credit', 'status' => 1, 'is_default' => 0, 'created_by' => 1]
        ]);
    }
}
