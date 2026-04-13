<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'      => 'Super Admin',
                'password'  => bcrypt('password'),
                'is_active' => true,
            ]
        );

        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->sync([$adminRole->id]);
    }
}
