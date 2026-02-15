<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        $manager = User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'manager',
                'password' => Hash::make('manager123'),
                'status' => 'active',
            ]
        );
        $manager->syncRoles([$managerRole]);

        $staff = User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'staff',
                'password' => Hash::make('staff123'),
                'status' => 'active',
            ]
        );
        $staff->syncRoles([$staffRole]);
    }
}

