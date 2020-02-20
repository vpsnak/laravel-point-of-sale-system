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
                'user_id' => 1,
                'barcode' => '389G1848LAAFHHALHP1905359716',
                'active' => true,
                'pos_terminal_ip' => '10.8.0.2',
                'pos_terminal_port' => '9790',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Main Cash Register 2',
                'store_id' => 1,
                'user_id' => 1,
                'barcode' => '389G1848LAAFHHALHP1929385706',
                'active' => true,
                'pos_terminal_ip' => '192.168.18.129',
                'pos_terminal_port' => '9790',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Main Cash Register 3',
                'store_id' => 1,
                'user_id' => 1,
                'barcode' => 'CASHIER1',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Main Cash Register 666',
                'store_id' => 2,
                'user_id' => 1,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
