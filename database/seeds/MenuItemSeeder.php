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

        $dashboard = new MenuItem([
            'title' => 'Dashboard',
            'action' => ['link' => '/'],
            'icon' =>  'dashboard',
            'location' => 'side_menu'
        ]);

        $sales = new MenuItem([
            'title' => 'Sales',
            'action' => ['link' => 'sales'],
            'icon' =>  'mdi-cart',
            'location' => 'side_menu'
        ]);

        $orders = new MenuItem([
            'title' => 'Orders',
            'action' => ['link' => 'orders'],
            'icon' =>  'mdi-buffer',
            'location' => 'side_menu'
        ]);

        $customers = new MenuItem([
            'title' => 'Customers',
            'action' => ['link' => 'customers'],
            'icon' =>  'mdi-account-group',
            'location' => 'side_menu'
        ]);

        $products = new MenuItem([
            'title' => 'Products',
            'action' => ['link' => 'products'],
            'icon' =>  'mdi-package-variant',
            'location' => 'side_menu'
        ]);

        $categories = new MenuItem([
            'title' => 'Categories',
            'action' => ['link' => 'categories'],
            'icon' =>  'mdi-inbox-multiple',
            'location' => 'side_menu'
        ]);

        $giftCards = new MenuItem([
            'title' => 'Gift Cards',
            'action' => ['link' => 'gift-cards'],
            'icon' =>  'mdi-wallet-giftcard',
            'location' => 'side_menu'
        ]);

        $coupons = new MenuItem([
            'title' => 'Coupons',
            'action' => ['link' => 'coupons'],
            'icon' =>  'mdi-ticket-percent',
            'location' => 'side_menu'
        ]);

        $cashRegisters = new MenuItem([
            'title' => 'Cash registers',
            'action' => ['link' => 'cash-registers'],
            'icon' =>  'mdi-cash-register',
            'location' => 'side_menu'
        ]);

        $stores = new MenuItem([
            'title' => 'Stores',
            'action' => ['link' => 'stores'],
            'icon' =>  'mdi-store',
            'location' => 'side_menu'
        ]);

        $storePickups = new MenuItem([
            'title' => 'Store pickups',
            'action' => ['link' => 'store-pickups'],
            'icon' =>  'mdi-storefront',
            'location' => 'side_menu'
        ]);

        $taxes = new MenuItem([
            'title' => 'Taxes',
            'action' => ['link' => 'taxes'],
            'icon' =>  'mdi-sack-percent',
            'location' => 'side_menu'
        ]);

        $reports = new MenuItem([
            'title' => 'Reports',
            'action' => ['link' => 'reports'],
            'icon' =>  'mdi-chart-areaspline',
            'location' => 'side_menu'
        ]);

        $users = new MenuItem([
            'title' => 'Users',
            'action' => ['link' => 'users'],
            'icon' =>  'mdi-account-multiple',
            'location' => 'side_menu'
        ]);

        $sign_out = new MenuItem([
            'title' => 'Sign out',
            'action' => ['link' => '/logout'],
            'icon' =>  'mdi-logout-variant',
            'location' => 'top_menu'
        ]);

        $change_password = new MenuItem([
            'title' => 'Change password',
            'action' => ['method' => 'changePasswordDialog'],
            'icon' =>  'mdi-key',
            'location' => 'top_menu'
        ]);

        //side menu
        $dashboard->save();
        $sales->save();
        $orders->save();
        $customers->save();
        $products->save();
        $categories->save();
        $giftCards->save();
        $coupons->save();
        $cashRegisters->save();
        $stores->save();
        $storePickups->save();
        $taxes->save();
        $reports->save();
        $users->save();

        $dashboard->roles()->sync($all);
        $sales->roles()->sync($all);
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

        //top menu
        $sign_out->save();
        $change_password->save();

        $sign_out->roles()->sync($all);
        $change_password->roles()->sync($all);
    }
}
