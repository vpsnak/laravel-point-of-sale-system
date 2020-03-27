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
        DB::table('users')->insert([
            'name' => 'Pavlos Kafritsas',
            'username' => 'deyjandi',
            'email' => 'pkafritsas@webo2.gr',
            'phone' => '777777777A',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::whereEmail('pkafritsas@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Nikos Papaioannou',
            'username' => 'kalmah',
            'email' => 'npapaioannou@webo2.gr',
            'phone' => '1111111111',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::whereEmail('npapaioannou@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Vaggelis Pallis',
            'username' => 'vpsnak',
            'email' => 'vpallis@webo2.gr',
            'phone' => '0000000000',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::whereEmail('vpallis@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Christos Gidersos',
            'username' => 'kitsos',
            'email' => 'cgidersos@webo2.gr',
            'phone' => '3333333333',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::whereEmail('cgidersos@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();
    }
}
