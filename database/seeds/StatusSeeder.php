<?php

use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stores')->insert([
            [
                'text' => 'Submitted',
                'value' => 'submitted',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-file-plus-outline'
            ],
            [
                'text' => 'Pending payment',
                'value' => 'pending_payment',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-currency-usd-off'
            ],
            [
                'text' => 'Paid',
                'value' => 'paid',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-currency-usd'
            ],
            [
                'text' => 'Completed',
                'value' => 'completed',
                'lock_order' => true,
                'color' => 'success',
                'icon' => 'mdi-checkbox-marked-circle-outline'
            ],
            [
                'text' => 'Printed',
                'value' => 'printed',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-printer-check'
            ],
            [
                'text' => 'Beign processed',
                'value' => 'being_processed',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-package-variant'
            ],
            [
                'text' => 'Processed',
                'value' => 'processed',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-package-variant-closed'
            ],
            [
                'text' => 'Out',
                'value' => 'out',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-truck-delivery-outline'
            ],
            [
                'text' => 'Canceled',
                'value' => 'cancel',
                'lock_order' => true,
                'color' => 'error',
                'icon' => 'mdi-close-circle-outline'
            ],
            [
                'text' => 'Pending review',
                'value' => 'pending_review',
                'lock_order' => false,
                'color' => '',
                'icon' => 'mdi-file-find-outline'
            ],
        ]);
    }
}
