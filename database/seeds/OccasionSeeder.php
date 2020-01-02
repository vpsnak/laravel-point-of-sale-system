<?php

use Illuminate\Database\Seeder;

class OccasionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('occasions')->insert([
            [
                'name' => 'Wedding',
            ],
            [
                'name' => 'Birthday',
            ],
            [
                'name' => 'Holiday',
            ],
            [
                'name' => 'Anniversary',
            ],
            [
                'name' => 'Business',
            ],
            [
                'name' => 'New Baby',
            ],
            [
                'name' => 'Get Well',
            ],
            [
                'name' => 'Funeral',
            ],
            [
                'name' => 'Other',
            ],
        ]);
    }
}
