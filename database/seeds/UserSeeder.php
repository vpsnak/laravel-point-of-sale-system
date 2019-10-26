<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 30)->make()->each(function ($user) {
            $user->save();
        });

        DB::table('users')->insert([
            'name' => 'Pavlos Kafritsas',
            'email' => 'pkafritsas@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        DB::table('users')->insert([
            'name' => 'Nikos Papaioannou',
            'email' => 'npapaioannou@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        DB::table('users')->insert([
            'name' => 'Vaggelis Pallis',
            'email' => 'vpallis@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        DB::table('users')->insert([
            'name' => 'Christos Gidersos',
            'email' => 'cgidersos@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
    }
}
