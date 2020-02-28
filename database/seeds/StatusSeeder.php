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
            ],
            [
                'text' => 'Pending payment',
                'value' => 'pending_payment',
                'lock_order' => false,
            ],
            [
                'text' => 'Paid',
                'value' => 'paid',
                'lock_order' => false,
            ],
            [
                'text' => 'Completed',
                'value' => 'completed',
                'lock_order' => false,
            ],
            [
                'text' => 'Printed',
                'value' => 'printed',
                'lock_order' => false,
            ],
            [
                'text' => 'Beign processed',
                'value' => 'being_processed',
                'lock_order' => false,
            ],
            [
                'text' => 'Processed',
                'value' => 'processed',
                'lock_order' => false,
            ],
            [
                'text' => 'Routed',
                'value' => 'routed',
                'lock_order' => false,
            ],
            [
                'text' => 'Out',
                'value' => 'out',
                'lock_order' => false,
            ],
            [
                'text' => 'Delivered',
                'value' => 'delivered',
                'lock_order' => true,
            ],
            [
                'text' => 'Canceled',
                'value' => 'cancel',
                'lock_order' => true,
            ],
            [
                'text' => 'Pending review',
                'value' => 'pending_review',
                'lock_order' => false,
            ],
        ]);
    }
}
