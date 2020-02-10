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
        /*
         * Roles map
         * admin            id: 1
         * cashier          id: 2
         * store_manager    id: 3
        */
        $admin = 1;
        $cashier = 2;
        $store_manager = 3;

        $all = [$admin, $cashier, $store_manager];

        $dashboard = new MenuItem([
            'title' => 'Dashboard',
            'action' => ['link' => '/'],
            'icon' =>  'dashboard',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sales = new MenuItem([
            'title' => 'Sales',
            'action' => ['link' => 'sales'],
            'icon' =>  'mdi-cart',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orders = new MenuItem([
            'title' => 'Orders',
            'action' => ['link' => 'orders'],
            'icon' =>  'mdi-buffer',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customers = new MenuItem([
            'title' => 'Customers',
            'action' => ['link' => 'customers'],
            'icon' =>  'mdi-account-group',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $addresses = new MenuItem([
            'title' => 'Addresses',
            'action' => ['link' => 'addresses'],
            'icon' =>  'mdi-account-card-details',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $products = new MenuItem([
            'title' => 'Products',
            'action' => ['link' => 'products'],
            'icon' =>  'mdi-package-variant',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categories = new MenuItem([
            'title' => 'Categories',
            'action' => ['link' => 'categories'],
            'icon' =>  'mdi-inbox-multiple',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $giftCards = new MenuItem([
            'title' => 'Gift Cards',
            'action' => ['link' => 'gift-cards'],
            'icon' =>  'mdi-wallet-giftcard',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $coupons = new MenuItem([
            'title' => 'Coupons',
            'action' => ['link' => 'coupons'],
            'icon' =>  'mdi-ticket-percent',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cashRegisters = new MenuItem([
            'title' => 'Cash registers',
            'action' => ['link' => 'cash-registers'],
            'icon' =>  'mdi-cash-register',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stores = new MenuItem([
            'title' => 'Stores',
            'action' => ['link' => 'stores'],
            'icon' =>  'mdi-store',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $storePickups = new MenuItem([
            'title' => 'Store pickups',
            'action' => ['link' => 'store-pickups'],
            'icon' =>  'mdi-storefront',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $companies = new MenuItem([
            'title' => 'Companies',
            'action' => ['link' => 'companies'],
            'icon' =>  'mdi-domain',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $taxes = new MenuItem([
            'title' => 'Taxes',
            'action' => ['link' => 'taxes'],
            'icon' =>  'mdi-sack-percent',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $reports = new MenuItem([
            'title' => 'Reports',
            'action' => ['link' => 'reports'],
            'icon' =>  'mdi-file-document-box',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $users = new MenuItem([
            'title' => 'Users',
            'action' => ['link' => 'users'],
            'icon' =>  'mdi-account-multiple',
            'location' => 'side_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sign_out = new MenuItem([
            'title' => 'Sign out',
            'action' => ['link' => '/logout'],
            'icon' =>  'mdi-logout-variant',
            'location' => 'top_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $change_password = new MenuItem([
            'title' => 'Change password',
            'action' => ['method' => 'changePasswordDialog'],
            'icon' =>  'mdi-key',
            'location' => 'top_menu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //side menu
        $dashboard->save();
        $sales->save();
        $orders->save();
        $customers->save();
        $addresses->save();
        $products->save();
        $categories->save();
        $giftCards->save();
        $coupons->save();
        $cashRegisters->save();
        $stores->save();
        $storePickups->save();
        $companies->save();
        $taxes->save();
        $reports->save();
        $users->save();

        //top menu
        $sign_out->save();
        $change_password->save();

        //side menu
        $dashboard->roles()->sync($all);
        $sales->roles()->sync($all);
        $orders->roles()->sync($all);
        $customers->roles()->sync($all);
        $addresses->roles()->sync($all);
        $products->roles()->sync($all);
        $categories->roles()->sync($all);

        $giftCards->roles()->sync($admin);
        $coupons->roles()->sync($admin);
        $cashRegisters->roles()->sync($admin);
        $stores->roles()->sync($admin);
        $storePickups->roles()->sync($admin);
        $companies->roles()->sync($admin);
        $taxes->roles()->sync($admin);

        $reports->roles()->sync([$admin, $store_manager]);

        $users->roles()->sync($admin);

        //top menu
        $sign_out->roles()->sync($all);
        $change_password->roles()->sync($all);
    }
}
