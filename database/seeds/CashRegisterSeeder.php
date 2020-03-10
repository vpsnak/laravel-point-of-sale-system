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
                'name' => '96th - Cash register 1',
                'store_id' => 1,
                'barcode' => '389G1848LAAFHHALHP1905359716',
                'active' => true,
                'pos_terminal_ip' => '10.8.0.2',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => '96th - Cash register 2',
                'store_id' => 1,
                'barcode' => '389G1848LAAFHHALHP1929385706',
                'active' => true,
                'pos_terminal_ip' => '192.168.18.129',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => '87th st - Cash register 1',
                'store_id' => 2,
                'barcode' => 'CASHIER1',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'Prince St - Cash register 1',
                'store_id' => 3,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'CS - Cash register 1',
                'store_id' => 4,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'CS - Cash register 2',
                'store_id' => 4,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'CS - Cash register 3',
                'store_id' => 4,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'CS - Cash register 4',
                'store_id' => 4,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'CS - Cash register 5',
                'store_id' => 4,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'CS - Cash register 6',
                'store_id' => 4,
                'barcode' => 'CASHIER2',
                'active' => true,
                'pos_terminal_ip' => '192.168.1.91',
                'pos_terminal_port' => '9790'
            ],
            [
                'name' => 'Demo Cash Register',
                'store_id' => 5,
                'barcode' => 'DEMO',
                'active' => true,
                'pos_terminal_ip' => '192.168.18.129',
                'pos_terminal_port' => '9790'
            ],
        ]);
    }
}
