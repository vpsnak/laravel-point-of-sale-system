<?php

use App\Tax;
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
            'name' => '8.875% Tax',
            'percentage' => 8.875,
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
