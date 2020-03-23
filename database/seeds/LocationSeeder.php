<?php

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('locations')->insert([
            [
                'name' => 'Funeral Home',
                'icon' => '',
            ],
            [
                'name' => 'Church',
                'icon' => 'mdi-church',
            ],
            [
                'name' => 'Reception Hall',
                'icon' => '',
            ],
            [
                'name' => 'Hospital',
                'icon' => 'mdi-hospital-building',
            ],
            [
                'name' => 'Business',
                'icon' => 'mdi-office-building',
            ],
            [
                'name' => 'Home',
                'icon' => 'mdi-home',
            ],
            [
                'name' => 'Apartment',
                'icon' => 'mdi-home-city',
            ],
            [
                'name' => 'Hotel',
                'icon' => 'mdi-bed',
            ],
            [
                'name' => 'International City',
                'icon' => 'mdi-earth',
            ],
            [
                'name' => 'School',
                'icon' => 'mdi-school',
            ],
            [
                'name' => 'Pick Up or Will Call',
                'icon' => 'mdi-phone',
            ],
            [
                'name' => 'Other',
                'icon' => '',
            ],
        ]);
    }
}
