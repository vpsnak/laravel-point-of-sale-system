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
                'created_at' => now()
            ],
        
        ]);
    }
}
