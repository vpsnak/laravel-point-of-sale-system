<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashRegisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cash_registers')->insert([
            [
                'name' => 'Main Cash Register',
                'store_id' => 1,
                'created_by' => 1,
                'barcode' => '',
                'created_at' => now()
            ],
            [
                'name' => 'Main Cash Register 2',
                'store_id' => 1,
                'created_by' => 1,
                'barcode' => 'CASHIER-3',
                'created_at' => now()
            ],
            [
                'name' => 'Main Cash Register 3',
                'store_id' => 1,
                'created_by' => 1,
                'barcode' => 'CASHIER-1',
                'created_at' => now()
            ],
            [
                'name' => 'Main Cash Register 666',
                'store_id' => 2,
                'barcode' => 'CASHIER-2',
                'created_at' => now()
            ],
        ]);
    }
}
