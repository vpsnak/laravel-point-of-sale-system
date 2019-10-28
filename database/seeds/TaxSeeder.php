<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('taxes')->insert([
            'name' => 'Zero Tax',
            'percentage' => 0,
        ]);
        DB::table('taxes')->insert([
            'name' => '8% Tax',
            'percentage' => 8,
        ]);
        DB::table('taxes')->insert([
            'name' => '24% Tax',
            'percentage' => 24,
        ]);
    }
}
