<?php

use App\Discount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coupons')->insert([
            [
                'name' => 'Zero Use Coupon',
                'code' => 'zero',
                'uses' => 0,
                'discount' => json_encode([
                    'type' => 'flat',
                    'amount' => 10
                ]),
                'from' => Carbon::now(),
                'to' => Carbon::now()->addMonth(),
            ],
            [
                'name' => 'Dev Coupon',
                'code' => 'dev',
                'uses' => 10000,
                'discount' => json_encode([
                    'type' => 'flat',
                    'amount' => 10
                ]),
                'from' => Carbon::now(),
                'to' => Carbon::now()->addMonth(),
            ],
            [
                'name' => 'Dev Coupon',
                'code' => 'percentage',
                'uses' => 10000,
                'discount' => json_encode([
                    'type' => 'percentage',
                    'amount' => 10
                ]),
                'from' => Carbon::now(),
                'to' => Carbon::now()->addMonth(),
            ],
            [
                'name' => 'Past Date Coupon',
                'code' => 'past',
                'uses' => 100000,
                'discount' => json_encode([
                    'type' => 'flat',
                    'amount' => 10
                ]),
                'from' => Carbon::now(),
                'to' => Carbon::now(),
            ],
            [
                'name' => 'Future Date Coupon',
                'code' => 'future',
                'uses' => 100000,
                'discount' => json_encode([
                    'type' => 'flat',
                    'amount' => 10
                ]),
                'from' => Carbon::now()->addYear(),
                'to' => Carbon::now()->addYear(),
            ],
        ]);
    }
}
