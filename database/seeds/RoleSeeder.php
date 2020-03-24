<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        foreach (config('roles') as $role_name => $role_text) {
            Role::create([
                'name' => $role_name,
                'text' => Str::of($role_text)->replace('_', ' ')
            ]);
        }
    }
}
