<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(OauthClientsSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MasAccountSeeder::class);
        $this->call(MenuItemsSeeder::class);
        $this->call(PaymentType::class);
        $this->call(StorePickupSeeder::class);

        $this->call(CompanySeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(BankAccountSeeder::class);
        $this->call(CashRegisterSeeder::class);

        // $this->call(ShippingSeeder::class);

        if (config('app.env') === 'local') {
            // $this->call(CustomerSeeder::class);
            // $this->call(ProductSeeder::class);
            // $this->call(CategorySeeder::class);
        }

        $this->call(GiftcardSeeder::class);
        $this->call(CouponSeeder::class);

        $this->call(PlantshedPostCodes::class);
        $this->call(PlantshedAreasSeeder::class);
    }
}
