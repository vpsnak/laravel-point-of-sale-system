<?php

use App\DeliveryTimeSlot;
use App\PostalCode;
use App\PostalCodeGrid;
use App\PostalGroup;
use App\TimeSlotGrid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantshedPostCodes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PostalCode::unguard();
        PostalGroup::unguard();
        PostalCodeGrid::unguard();
        DeliveryTimeSlot::unguard();
        TimeSlotGrid::unguard();

        $codes = 'database/seeds/sql/postalcode.sql';
        $groups = 'database/seeds/sql/postalgroup.sql';
        $grid = 'database/seeds/sql/postalcodegrid.sql';
        $timeslot = 'database/seeds/sql/deliverytimeslot.sql';
        $timeslot_grid = 'database/seeds/sql/timeslotgrid.sql';
        DB::unprepared(file_get_contents($codes));
        DB::unprepared(file_get_contents($groups));
        DB::unprepared(file_get_contents($grid));
        DB::unprepared(file_get_contents($timeslot));
        DB::unprepared(file_get_contents($timeslot_grid));

        $this->command->info('Plantshed Timeslots seeded!');
    }
}
