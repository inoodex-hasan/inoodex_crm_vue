<?php

namespace Database\Seeders;

use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles Definition
        $roles = [
            'super-admin'       => 'Super Admin',
            'admin'             => 'Administrator',
            'marketing'         => 'Marketing',
            'consultant'        => 'Consultant',
            'accountant'        => 'Accountant',
            'editor'            => 'Editor',
            'application'       => 'Application',
            'digital-marketing' => 'Digital Marketing',
            'user'              => 'User',
        ];

        $roleModels = [];
        foreach ($roles as $slug => $name) {
            $roleModels[$slug] = Role::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'is_active' => true]
            );
        }

        // 2. Privileges Definition & Mapping to Roles
        $privileges = [
            [
                'slug'        => '*',
                'name'        => 'Wildcard',
                'description' => 'Grants every privilege.',
                'roles'       => ['super-admin', 'admin'],
            ],
            [
                'slug'        => '*marketing',
                'name'        => 'Marketing',
                'description' => 'Allows access to marketing modules.',
                'roles'       => ['marketing', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*consultant',
                'name'        => 'Consultant',
                'description' => 'Allows student counseling and consulting.',
                'roles'       => ['consultant', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*accountant',
                'name'        => 'Accountant',
                'description' => 'Allows finance, accounts, and ledger access.',
                'roles'       => ['accountant', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*editor',
                'name'        => 'Editor',
                'description' => 'Allows document and content editing.',
                'roles'       => ['editor', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*application',
                'name'        => 'Application',
                'description' => 'Allows student application processing.',
                'roles'       => ['application', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*payment',
                'name'        => 'Payment',
                'description' => 'Allows collecting and managing student payments.',
                'roles'       => ['accountant', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*comission',
                'name'        => 'Comission',
                'description' => 'Allows claiming and approving commissions.',
                'roles'       => ['accountant', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*invoice',
                'name'        => 'Invoice',
                'description' => 'Allows viewing and creating student invoices.',
                'roles'       => ['consultant', 'accountant', 'super-admin', 'admin'],
            ],
            [
                'slug'        => '*digital_marketing',
                'name'        => 'Digital Marketing',
                'description' => 'Allows social media and digital campaign access.',
                'roles'       => ['digital-marketing', 'super-admin', 'admin'],
            ],
            [
                'slug'        => 'report.generate',
                'name'        => 'Generate Reports',
                'description' => 'Allows generating system-wide reports.',
                'roles'       => ['admin', 'super-admin', 'accountant'],
            ],
            [
                'slug'        => 'users.manage',
                'name'        => 'Manage Users',
                'description' => 'Allows creating, editing, and deleting users.',
                'roles'       => ['admin', 'super-admin'],
            ],
            [
                'slug'        => 'roles.manage',
                'name'        => 'Manage Roles',
                'description' => 'Allows managing roles and privileges.',
                'roles'       => ['super-admin'],
            ],
            [
                'slug'        => 'billing.view',
                'name'        => 'View Billing',
                'description' => 'Allows viewing billing statements.',
                'roles'       => ['admin', 'user', 'super-admin'],
            ],
        ];

        foreach ($privileges as $def) {
            $privilege = Privilege::updateOrCreate(
                ['slug' => $def['slug']],
                [
                    'name'        => $def['name'],
                    'description' => $def['description'] ?? null,
                ]
            );

            $attachRoleIds = [];
            foreach ($def['roles'] as $roleSlug) {
                if (isset($roleModels[$roleSlug])) {
                    $attachRoleIds[] = $roleModels[$roleSlug]->id;
                }
            }

            if (!empty($attachRoleIds)) {
                $privilege->roles()->syncWithoutDetaching($attachRoleIds);
            }
        }
    }
}
