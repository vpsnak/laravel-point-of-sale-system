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
            ],
            [
                'name' => 'Church',
            ],
            [
                'name' => 'Reception Hall',
            ],
            [
                'name' => 'Hospital',
            ],
            [
                'name' => 'Business',
            ],
            [
                'name' => 'Home',
            ],
            [
                'name' => 'Apartment',
            ],
            [
                'name' => 'Hotel',
            ],
            [
                'name' => 'International City',
            ],
            [
                'name' => 'School',
            ],
            [
                'name' => 'Other',
            ],
            [
                'name' => 'Pick Up or Will Call',
            ],
        ]);
    }
}
