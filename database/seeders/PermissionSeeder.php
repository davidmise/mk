<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'dashboard', 'description' => 'Access the admin dashboard'],

            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users', 'description' => 'View user list and details'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'users', 'description' => 'Create new user accounts'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'users', 'description' => 'Edit user accounts and permissions'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'users', 'description' => 'Delete user accounts'],

            // Roles
            ['name' => 'View Roles', 'slug' => 'roles.view', 'group' => 'roles', 'description' => 'View roles and permissions'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'group' => 'roles', 'description' => 'Create new roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'group' => 'roles', 'description' => 'Edit roles and assign permissions'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'group' => 'roles', 'description' => 'Delete roles'],

            // Bookings
            ['name' => 'View Bookings', 'slug' => 'bookings.view', 'group' => 'bookings', 'description' => 'View booking list and details'],
            ['name' => 'Create Bookings', 'slug' => 'bookings.create', 'group' => 'bookings', 'description' => 'Create new bookings'],
            ['name' => 'Edit Bookings', 'slug' => 'bookings.edit', 'group' => 'bookings', 'description' => 'Edit bookings, check-in/out, payments'],
            ['name' => 'Delete Bookings', 'slug' => 'bookings.delete', 'group' => 'bookings', 'description' => 'Delete bookings'],

            // Rooms
            ['name' => 'View Rooms', 'slug' => 'rooms.view', 'group' => 'rooms', 'description' => 'View room inventory'],
            ['name' => 'Create Rooms', 'slug' => 'rooms.create', 'group' => 'rooms', 'description' => 'Add new rooms'],
            ['name' => 'Edit Rooms', 'slug' => 'rooms.edit', 'group' => 'rooms', 'description' => 'Edit room details and status'],
            ['name' => 'Delete Rooms', 'slug' => 'rooms.delete', 'group' => 'rooms', 'description' => 'Delete rooms'],

            // Room Types
            ['name' => 'View Room Types', 'slug' => 'room_types.view', 'group' => 'room_types', 'description' => 'View room type configurations'],
            ['name' => 'Create Room Types', 'slug' => 'room_types.create', 'group' => 'room_types', 'description' => 'Create new room types'],
            ['name' => 'Edit Room Types', 'slug' => 'room_types.edit', 'group' => 'room_types', 'description' => 'Edit room types and pricing'],
            ['name' => 'Delete Room Types', 'slug' => 'room_types.delete', 'group' => 'room_types', 'description' => 'Delete room types'],

            // Amenities
            ['name' => 'View Amenities', 'slug' => 'amenities.view', 'group' => 'amenities', 'description' => 'View amenities list'],
            ['name' => 'Create Amenities', 'slug' => 'amenities.create', 'group' => 'amenities', 'description' => 'Add new amenities'],
            ['name' => 'Edit Amenities', 'slug' => 'amenities.edit', 'group' => 'amenities', 'description' => 'Edit amenities'],
            ['name' => 'Delete Amenities', 'slug' => 'amenities.delete', 'group' => 'amenities', 'description' => 'Delete amenities'],

            // Guests
            ['name' => 'View Guests', 'slug' => 'guests.view', 'group' => 'guests', 'description' => 'View guest profiles'],
            ['name' => 'Create Guests', 'slug' => 'guests.create', 'group' => 'guests', 'description' => 'Create guest profiles'],
            ['name' => 'Edit Guests', 'slug' => 'guests.edit', 'group' => 'guests', 'description' => 'Edit guest profiles'],
            ['name' => 'Delete Guests', 'slug' => 'guests.delete', 'group' => 'guests', 'description' => 'Delete guest profiles'],

            // Pages (CMS)
            ['name' => 'View Pages', 'slug' => 'pages.view', 'group' => 'pages', 'description' => 'View website pages'],
            ['name' => 'Create Pages', 'slug' => 'pages.create', 'group' => 'pages', 'description' => 'Create new pages'],
            ['name' => 'Edit Pages', 'slug' => 'pages.edit', 'group' => 'pages', 'description' => 'Edit page content'],
            ['name' => 'Delete Pages', 'slug' => 'pages.delete', 'group' => 'pages', 'description' => 'Delete pages'],

            // News
            ['name' => 'View News', 'slug' => 'news.view', 'group' => 'news', 'description' => 'View news articles'],
            ['name' => 'Create News', 'slug' => 'news.create', 'group' => 'news', 'description' => 'Create news articles'],
            ['name' => 'Edit News', 'slug' => 'news.edit', 'group' => 'news', 'description' => 'Edit and publish articles'],
            ['name' => 'Delete News', 'slug' => 'news.delete', 'group' => 'news', 'description' => 'Delete news articles'],

            // Gallery
            ['name' => 'View Gallery', 'slug' => 'gallery.view', 'group' => 'gallery', 'description' => 'View gallery images'],
            ['name' => 'Create Gallery', 'slug' => 'gallery.create', 'group' => 'gallery', 'description' => 'Upload gallery images'],
            ['name' => 'Edit Gallery', 'slug' => 'gallery.edit', 'group' => 'gallery', 'description' => 'Edit gallery images'],
            ['name' => 'Delete Gallery', 'slug' => 'gallery.delete', 'group' => 'gallery', 'description' => 'Delete gallery images'],

            // Calendar
            ['name' => 'View Calendar', 'slug' => 'calendar.view', 'group' => 'calendar', 'description' => 'View availability calendar'],
            ['name' => 'Edit Calendar', 'slug' => 'calendar.edit', 'group' => 'calendar', 'description' => 'Manage calendar and room status'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'group' => 'reports', 'description' => 'Access reports and analytics'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'group' => 'reports', 'description' => 'Export reports to CSV'],

            // Settings
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'settings', 'description' => 'View system settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'group' => 'settings', 'description' => 'Modify system settings'],

            // Activity Logs
            ['name' => 'View Activity Logs', 'slug' => 'activity_logs.view', 'group' => 'activity_logs', 'description' => 'View system activity logs'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Permissions seeded and assigned successfully!');
    }

    /**
     * Assign permissions to roles.
     */
    protected function assignPermissionsToRoles(): void
    {
        $allPermissions = Permission::pluck('id')->toArray();

        // Super Admin gets all permissions
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync($allPermissions);
        }

        // Admin gets most permissions except some system-level ones
        $admin = Role::where('slug', 'admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereNotIn('slug', [
                'roles.delete', // Admins cannot delete roles
            ])->pluck('id')->toArray();
            $admin->permissions()->sync($adminPermissions);
        }

        // Manager permissions
        $manager = Role::where('slug', 'manager')->first();
        if ($manager) {
            $managerPermissions = Permission::whereIn('group', [
                'dashboard', 'bookings', 'rooms', 'room_types', 'amenities',
                'guests', 'calendar', 'reports'
            ])->pluck('id')->toArray();
            $manager->permissions()->sync($managerPermissions);
        }

        // Receptionist permissions
        $receptionist = Role::where('slug', 'receptionist')->first();
        if ($receptionist) {
            $receptionistPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'bookings.view', 'bookings.create', 'bookings.edit',
                'rooms.view',
                'room_types.view',
                'guests.view', 'guests.create', 'guests.edit',
                'calendar.view', 'calendar.edit',
            ])->pluck('id')->toArray();
            $receptionist->permissions()->sync($receptionistPermissions);
        }

        // Content Editor permissions
        $contentEditor = Role::where('slug', 'content-editor')->first();
        if ($contentEditor) {
            $contentPermissions = Permission::whereIn('group', [
                'dashboard', 'pages', 'news', 'gallery'
            ])->pluck('id')->toArray();
            $contentEditor->permissions()->sync($contentPermissions);
        }
    }
}
