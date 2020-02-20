<?php

use App\StorePickup;
use Illuminate\Database\Seeder;

class StorePickupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StorePickup::create([
            'name' => '96 Dromoi',
            'street' => '209 West 96th street',
            'region_id' => 43, // New York
        ]);
        StorePickup::create([
            'name' => '555 kolomvoi',
            'street' => '555 Columbus Ave',
            'region_id' => 43, // New York
        ]);
        StorePickup::create([
            'name' => 'Enas Prigkipas',
            'street' => '1 Prince St.',
            'street1' => '(at Bowery)',
            'region_id' => 43, // New York
        ]);
    }
}
