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
                'name' => 'Green 2 Green Corp',
                'tax_number' => ''
            ],
            [
                'name' => 'Plantshed 87 Corp',
                'tax_number' => ''
            ],
            [
                'name' => 'Plantshed Prince Inc',
                'tax_number' => ''
            ],
            // for elavon demo acc
            [
                'name' => 'Demo',
                'tax_number' => ''
            ]
        ]);
    }
}
