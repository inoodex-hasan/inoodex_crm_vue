<?php

namespace Database\Seeders;

use App\Models\User;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed all roles, privileges, and role-privilege assignments
        $this->call(RoleAndPrivilegeSeeder::class);

        // 2. Create the default Super Admin user
        $admin = User::firstOrCreate(
            ['email' => 'hello@inoodex.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('hello@inoodex.com'),
                'plain_password' => 'hello@inoodex.com',
            ]
        );

        // 3. Assign super-admin role
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $admin->assignRole($superAdminRole);
        }
    }
}
