<?php

use Illuminate\Database\Seeder;
use App\User;

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
            $user->assignRole('cashier');
            $user->save();
        });

        DB::table('users')->insert([
            'name' => 'Pavlos Kafritsas',
            'email' => 'pkafritsas@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
        $user = User::whereEmail('pkafritsas@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Nikos Papaioannou',
            'email' => 'npapaioannou@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
        $user = User::whereEmail('npapaioannou@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Vaggelis Pallis',
            'email' => 'vpallis@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
        $user = User::whereEmail('vpallis@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Christos Gidersos',
            'email' => 'cgidersos@webo2.gr',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
        $user = User::whereEmail('cgidersos@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();
    }
}
