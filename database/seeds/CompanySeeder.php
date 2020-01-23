<?php

use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            [
                'name' => 'CO1-PLANTSHED NY FLOWERS',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'CO2-PLANTSHED NY FLOWERS',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'CO3-PLANTSHED NY FLOWERS',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
