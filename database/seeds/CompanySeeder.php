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
                'tax_number' => '1312'
            ],
            [
                'name' => 'CO2-PLANTSHED NY FLOWERS',
                'tax_number' => '1213'
            ],
            [
                'name' => 'CO3-PLANTSHED NY FLOWERS',
                'tax_number' => '1234'
            ]
        ]);
    }
}
