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
                'phone' => '(212)662-4400',
                'street' => '209 w 96th st',
                'postal_code' => '10025',
                'city' => 'NEW YORK NY',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'PlantShed Cafe at 87th Street',
                'phone' => '212-662-4400',
                'street' => '87th Street',
                'postal_code' => '10026',
                'city' => 'NEW YORK NY',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'PlantShed Cafe at Prince Street',
                'phone' => '212-662-4400',
                'street' => 'Prince Street',
                'postal_code' => '10027',
                'city' => 'NEW YORK NY',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
