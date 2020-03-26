<?php

use App\MenuItem;
use Illuminate\Database\Seeder;


class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $admin = 1;
        $cashier = 2;
        $store_manager = 3;

        $all = [$admin, $cashier, $store_manager];

        $dashboard = MenuItem::create([
            'title' => 'Dashboard',
            'action' => ['link' => '/'],
            'icon' =>  'dashboard',
            'location' => 'side_menu'
        ]);

        $sale = MenuItem::create([
            'title' => 'Sale',
            'action' => ['link' => 'sale'],
            'icon' =>  'mdi-cart',
            'location' => 'side_menu'
        ]);

        $orders = MenuItem::create([
            'title' => 'Orders',
            'action' => ['link' => 'orders'],
            'icon' =>  'mdi-file-multiple',
            'location' => 'side_menu'
        ]);

        $customers = MenuItem::create([
            'title' => 'Customers',
            'action' => ['link' => 'customers'],
            'icon' =>  'mdi-account-group',
            'location' => 'side_menu'
        ]);

        $products = MenuItem::create([
            'title' => 'Products',
            'action' => ['link' => 'products'],
            'icon' =>  'mdi-package-variant',
            'location' => 'side_menu'
        ]);

        $categories = MenuItem::create([
            'title' => 'Categories',
            'action' => ['link' => 'categories'],
            'icon' =>  'mdi-inbox-multiple',
            'location' => 'side_menu'
        ]);

        $giftCards = MenuItem::create([
            'title' => 'Gift Cards',
            'action' => ['link' => 'giftcards'],
            'icon' =>  'mdi-wallet-giftcard',
            'location' => 'side_menu'
        ]);

        $coupons = MenuItem::create([
            'title' => 'Coupons',
            'action' => ['link' => 'coupons'],
            'icon' =>  'mdi-ticket-percent',
            'location' => 'side_menu'
        ]);

        $cashRegisters = MenuItem::create([
            'title' => 'Cash registers',
            'action' => ['link' => 'cash-registers'],
            'icon' =>  'mdi-cash-register',
            'location' => 'side_menu'
        ]);

        $stores = MenuItem::create([
            'title' => 'Stores',
            'action' => ['link' => 'stores'],
            'icon' =>  'mdi-store',
            'location' => 'side_menu'
        ]);

        $storePickups = MenuItem::create([
            'title' => 'Store pickups',
            'action' => ['link' => 'store-pickups'],
            'icon' =>  'mdi-storefront',
            'location' => 'side_menu'
        ]);

        $taxes = MenuItem::create([
            'title' => 'Taxes',
            'action' => ['link' => 'taxes'],
            'icon' =>  'mdi-sack-percent',
            'location' => 'side_menu'
        ]);

        $reports = MenuItem::create([
            'title' => 'Reports',
            'action' => ['link' => 'reports'],
            'icon' =>  'mdi-chart-areaspline',
            'location' => 'side_menu'
        ]);

        $users = MenuItem::create([
            'title' => 'Users',
            'action' => ['link' => 'users'],
            'icon' =>  'mdi-account-multiple',
            'location' => 'side_menu'
        ]);

        $settings = MenuItem::create([
            'title' => 'Settings',
            'action' => ['link' => 'settings'],
            'icon' =>  'mdi-cog-outline',
            'location' => 'side_menu'
        ]);

        $sign_out = MenuItem::create([
            'title' => 'Sign out',
            'action' => ['link' => '/logout'],
            'icon' =>  'mdi-logout-variant',
            'location' => 'top_menu'
        ]);

        $change_password = MenuItem::create([
            'title' => 'Change password',
            'action' => ['method' => 'changePasswordDialog'],
            'icon' =>  'mdi-key',
            'location' => 'top_menu'
        ]);

        // side menu
        $dashboard->roles()->sync($all);
        $sale->roles()->sync($all);
        $orders->roles()->sync($all);
        $customers->roles()->sync($all);
        $products->roles()->sync($all);
        $categories->roles()->sync($all);
        $giftCards->roles()->sync($admin);
        $coupons->roles()->sync($admin);
        $cashRegisters->roles()->sync($admin);
        $stores->roles()->sync($admin);
        $storePickups->roles()->sync($admin);
        $taxes->roles()->sync($admin);
        $reports->roles()->sync([$admin, $store_manager]);
        $users->roles()->sync($admin);
        $settings->roles()->sync($admin);
        // top menu
        $sign_out->roles()->sync($all);
        $change_password->roles()->sync($all);
    }
}
