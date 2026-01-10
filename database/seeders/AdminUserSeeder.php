<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if (!$superAdminRole) {
            $this->command->error('Please run RoleSeeder first!');
            return;
        }

        // Create Super Admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@mkhotel.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Attach role via pivot table
        $superAdmin->roles()->sync([$superAdminRole->id]);

        $this->command->info("Super Admin created/updated:");
        $this->command->info("  Email: admin@mkhotel.com");
        $this->command->info("  Password: password");
        $this->command->warn("  ⚠️  Please change the default password after first login!");

        // Create sample users for each role (optional - for testing)
        if ($this->command->confirm('Would you like to create sample users for each role?', false)) {
            $this->createSampleUsers();
        }
    }

    /**
     * Create sample users for each role.
     */
    protected function createSampleUsers(): void
    {
        $roles = [
            'admin' => ['name' => 'Hotel Admin', 'email' => 'manager@mkhotel.com'],
            'manager' => ['name' => 'Operations Manager', 'email' => 'ops@mkhotel.com'],
            'receptionist' => ['name' => 'Front Desk', 'email' => 'reception@mkhotel.com'],
            'content-editor' => ['name' => 'Content Manager', 'email' => 'content@mkhotel.com'],
        ];

        foreach ($roles as $roleSlug => $userData) {
            $role = Role::where('slug', $roleSlug)->first();

            if ($role) {
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => Hash::make('password'),
                        'status' => 'active',
                        'email_verified_at' => now(),
                    ]
                );

                // Attach role via pivot table
                $user->roles()->sync([$role->id]);

                $this->command->info("  Created {$role->name}: {$userData['email']}");
            }
        }

        $this->command->info('');
        $this->command->warn('All sample users have password: password');
    }
}
