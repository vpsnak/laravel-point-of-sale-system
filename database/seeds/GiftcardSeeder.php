<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiftcardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('giftcards')->insert([
            [
                'name' => 'Zero Giftcard',
                'code' => 'zero',
                'enabled' => 1,
                'amount' => 0,
            ],
            [
                'name' => 'Dev Giftcard',
                'code' => 'dev',
                'enabled' => 1,
                'amount' => 999999,
            ],
            [
                'name' => 'Disabled Giftcard',
                'code' => 'disabled',
                'enabled' => 0,
                'amount' => 1337,
            ]
        ]);
    }
}
