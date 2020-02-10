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
            'name' => 'Dashboard',
            'action' => ['link' => '/'],
            'icon' =>  'dashboard',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sales = new MenuItem([
            'name' => 'Sales',
            'action' => ['link' => 'sales'],
            'icon' =>  'mdi-cart',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orders = new MenuItem([
            'name' => 'Orders',
            'action' => ['link' => 'orders'],
            'icon' =>  'mdi-buffer',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $customers = new MenuItem([
            'name' => 'Customers',
            'action' => ['link' => 'customers'],
            'icon' =>  'mdi-account-group',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $addresses = new MenuItem([
            'name' => 'Addresses',
            'action' => ['link' => 'addresses'],
            'icon' =>  'mdi-account-card-details',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $products = new MenuItem([
            'name' => 'Products',
            'action' => ['link' => 'products'],
            'icon' =>  'mdi-package-variant',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categories = new MenuItem([
            'name' => 'Categories',
            'action' => ['link' => 'categories'],
            'icon' =>  'mdi-inbox-multiple',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $giftCards = new MenuItem([
            'name' => 'Gift Cards',
            'action' => ['link' => 'gift-cards'],
            'icon' =>  'mdi-wallet-giftcard',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $coupons = new MenuItem([
            'name' => 'Coupons',
            'action' => ['link' => 'coupons'],
            'icon' =>  'mdi-ticket-percent',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cashRegisters = new MenuItem([
            'name' => 'Cash registers',
            'action' => ['link' => 'cash-registers'],
            'icon' =>  'mdi-cash-register',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stores = new MenuItem([
            'name' => 'Stores',
            'action' => ['link' => 'stores'],
            'icon' =>  'mdi-store',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $storePickups = new MenuItem([
            'name' => 'Store pickups',
            'action' => ['link' => 'store-pickups'],
            'icon' =>  'mdi-storefront',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $companies = new MenuItem([
            'name' => 'Companies',
            'action' => ['link' => 'companies'],
            'icon' =>  'mdi-domain',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $taxes = new MenuItem([
            'name' => 'Taxes',
            'action' => ['link' => 'taxes'],
            'icon' =>  'mdi-sack-percent',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $reports = new MenuItem([
            'name' => 'Reports',
            'action' => ['link' => 'reports'],
            'icon' =>  'mdi-file-document-box',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $users = new MenuItem([
            'name' => 'Users',
            'action' => ['link' => 'users'],
            'icon' =>  'mdi-account-multiple',
            'location' => 'sidemenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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
    }
}
