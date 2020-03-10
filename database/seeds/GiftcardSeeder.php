<?php

use App\Giftcard;
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
                'enabled' => now(),
                'price' => json_encode([
                    'amount' => 0,
                    'currency' => 'USD'
                ]),
            ],
            [
                'name' => 'Dev Giftcard',
                'code' => 'dev',
                'enabled' => now(),
                'price' => json_encode([
                    'amount' => 9999999,
                    'currency' => 'USD'
                ]),
            ],
            [
                'name' => 'Disabled Giftcard',
                'code' => 'disabled',
                'enabled' => false,
                'price' => json_encode([
                    'amount' => 100000,
                    'currency' => 'USD'
                ]),
            ]
        ]);
        foreach (range(90001, 90010) as $giftcard_code) {
            Giftcard::create([
                'name' => 'Inactive Giftcard',
                'code' => '@' . str_pad($giftcard_code, 10, '0', STR_PAD_LEFT),
                'enabled' => false,
                'price' => json_encode([
                    'amount' => 0,
                    'currency' => 'USD'
                ]),
            ]);
        }
    }
}
