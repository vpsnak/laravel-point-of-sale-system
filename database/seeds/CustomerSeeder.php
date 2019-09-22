<?php

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Customer::class, 30)->make()->each(function ($customer) {
            $customer->save();
            $address = factory(App\Address::class)->create();
            $customer->addresses()->attach($address);
            $address = factory(App\Address::class)->create();
            $customer->addresses()->attach($address);
        });
    }
}
