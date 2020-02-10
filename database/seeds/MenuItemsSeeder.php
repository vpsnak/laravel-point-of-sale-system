<?php

use Illuminate\Database\Seeder;


class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        DB::table('menu_items')->insert([
            'name' => 'Dashboard',
            'action' => '{ "href": "/"}',
            'icon' =>  'dashboard',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Sales',
            'action' => '{ "href": "sales"}',
            'icon' =>  'mdi-cart',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Orders',
            'action' => '{ "href": "orders"}',
            'icon' =>  'mdi-buffer',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Customers',
            'action' => '{ "href": "customers"}',
            'icon' =>  'mdi-account-group',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Addresses',
            'action' => '{ "href": "addresses"}',
            'icon' =>  'mdi-account-card-details',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Products',
            'action' => '{ "href": "products"}',
            'icon' =>  'mdi-package-variant',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Categories',
            'action' => '{ "href": "categories"}',
            'icon' =>  'mdi-inbox-multiple',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Gift Cards',
            'action' => '{ "href": "gift-cards"}',
            'icon' =>  'mdi-wallet-giftcard',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Coupons',
            'action' => '{ "href": "coupons"}',
            'icon' =>  'mdi-ticket-percent',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Cash registers',
            'action' => '{ "href": "cash-registers"}',
            'icon' =>  'mdi-cash-register',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Stores',
            'action' => '{ "href": "stores"}',
            'icon' =>  'mdi-store',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Store pickups',
            'action' => '{ "href": "store-pickups"}',
            'icon' =>  'mdi-storefront',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Companies',
            'action' => '{ "href": "companies"}',
            'icon' =>  'mdi-domain',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Taxes',
            'action' => '{ "href": "taxes"}',
            'icon' =>  'mdi-sack-percent',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Reports',
            'action' => '{ "href": "reports"}',
            'icon' =>  'mdi-file-document-box',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('menu_items')->insert([
            'name' => 'Users',
            'action' => '{ "href": "users"}',
            'icon' =>  'mdi-account-multiple',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
