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
        $this->call(CountrySeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MasAccountSeeder::class);
        $this->call(MenuItemSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(StorePickupSeeder::class);
        $this->call(StatusSeeder::class);

        $this->call(CompanySeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(BankAccountSeeder::class);
        $this->call(CashRegisterSeeder::class);

        $this->call(PlantshedPostCodes::class);

        if (config('app.env') === 'local') {
            $this->call(CustomerSeeder::class);
            $this->call(AddressSeeder::class);

            $this->call(GiftcardSeeder::class);
            $this->call(CouponSeeder::class);

            $this->call(ProductSeeder::class);
            $this->call(CategorySeeder::class);
        }
    }
}
