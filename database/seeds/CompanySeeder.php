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
                'name' => 'Company 1',
                'phone' => '123456789',
                'address' => 'asd 231',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Company 2',
                'phone' => '',
                'address' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Company 2',
                'phone' => '',
                'address' => '',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
