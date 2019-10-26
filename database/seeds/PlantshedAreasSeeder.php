<?php

use App\Country;
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
        Country::unguard();
        $regions = 'database/seeds/sql/regions.sql';
        $countries = 'database/seeds/sql/countries.sql';
        DB::unprepared(file_get_contents($regions));
        DB::unprepared(file_get_contents($countries));

        $this->command->info('Plantshed Areas seeded!');
    }
}
