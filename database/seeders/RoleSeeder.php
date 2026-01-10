<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions. Cannot be modified or deleted.',
                'level' => 100,
                'is_system' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access to manage hotel operations, users, and content.',
                'level' => 80,
                'is_system' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manage bookings, rooms, guests, and view reports.',
                'level' => 60,
                'is_system' => true,
            ],
            [
                'name' => 'Receptionist',
                'slug' => 'receptionist',
                'description' => 'Handle check-ins, check-outs, and basic booking operations.',
                'level' => 40,
                'is_system' => true,
            ],
            [
                'name' => 'Content Editor',
                'slug' => 'content-editor',
                'description' => 'Manage website content, pages, news articles, and gallery.',
                'level' => 20,
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
