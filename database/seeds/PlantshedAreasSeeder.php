<?php

use App\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantshedAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::unguard();
        $regions = 'database/seeds/sql/regions.sql';
        DB::unprepared(file_get_contents($regions));

        $this->command->info('Plantshed Areas seeded!');
    }
}
