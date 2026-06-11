<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Register the Room Management module (data-driven sidebar).
        $module = DB::table('modules')->where('name', 'Room Management')->first();
        if (!$module) {
            $moduleId = DB::table('modules')->insertGetId([
                'name' => 'Room Management',
                'description' => 'Manage room bookings, time billing, and QR food orders',
                'icon' => 'door-open',
                'route' => 'rooms.index',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $moduleId = $module->id;
        }

        // 2. Create a dedicated "Room Staff" role (Rooms-only), parallel to Cashier (POS-only).
        $role = DB::table('roles')->where('name', 'Room Staff')->first();
        if (!$role) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'Room Staff',
                'description' => 'Room Staff - Can manage room bookings and billing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $roleId = $role->id;
        }

        // 3. Grant the Room Management module to Admin and Room Staff.
        $adminId = DB::table('roles')->where('name', 'Admin')->value('id');

        foreach (array_filter([$adminId, $roleId]) as $rid) {
            DB::table('role_module')->insertOrIgnore([
                'role_id' => $rid,
                'module_id' => $moduleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $moduleId = DB::table('modules')->where('name', 'Room Management')->value('id');
        if ($moduleId) {
            DB::table('role_module')->where('module_id', $moduleId)->delete();
            DB::table('modules')->where('id', $moduleId)->delete();
        }

        $roleId = DB::table('roles')->where('name', 'Room Staff')->value('id');
        if ($roleId) {
            DB::table('role_module')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }
    }
};
