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
        ]);
        $user = User::whereEmail('pkafritsas@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Nikos Papaioannou',
            'username' => 'nikospap',
            'email' => 'npapaioannou@webo2.gr',
            'phone' => '1111111111',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
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
        ]);
        $user = User::whereEmail('cgidersos@webo2.gr')->first();
        $user->assignRole('admin');
        $user->save();

        DB::table('users')->insert([
            'name' => 'Cafetex',
            'username' => 'cafetex',
            'email' => 'cafetex@cafetex.gr',
            'phone' => '1231231230',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);
        $user = User::whereEmail('cafetex@cafetex.gr')->first();
        $user->assignRole('admin');
        $user->save();
    }
}
