<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'key' => 'theme_dark',
                'value' => null,
                'data' => null,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'theme_dark',
                'value' => false,
                'data' => null,
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'theme_dark',
                'value' => null,
                'data' => null,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'theme_dark',
                'value' => null,
                'data' => null,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
