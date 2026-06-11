<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create standalone history modules (not previously in the sidebar).
        $newModules = [
            ['name' => 'Order History', 'description' => 'View and reprint past orders', 'icon' => 'receipt', 'route' => 'order.history', 'sort_order' => 10],
            ['name' => 'Room History', 'description' => 'Past room bookings and bills', 'icon' => 'clock-rotate-left', 'route' => 'rooms.history', 'sort_order' => 11],
        ];
        foreach ($newModules as $m) {
            if (!DB::table('modules')->where('name', $m['name'])->exists()) {
                DB::table('modules')->insert($m + ['created_at' => now(), 'updated_at' => now()]);
            }
        }

        $assign = function (?int $roleId, array $moduleNames): void {
            if (!$roleId) {
                return;
            }
            foreach ($moduleNames as $name) {
                $moduleId = DB::table('modules')->where('name', $name)->value('id');
                if ($moduleId) {
                    DB::table('role_module')->insertOrIgnore([
                        'role_id' => $roleId,
                        'module_id' => $moduleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        };

        // 2. Room Staff sidebar + dashboard: the full room toolset.
        //    (Settings hub already contains Room Settings + Room QR Codes.)
        $roomStaffId = DB::table('roles')->where('name', 'Room Staff')->value('id');
        $assign($roomStaffId, [
            'Room Management',
            'Shifts & Till Management',
            'Reports',
            'Order History',
            'Room History',
            'Settings',
        ]);

        // 3. Admin also gets the two new standalone modules (already has the rest).
        $adminId = DB::table('roles')->where('name', 'Admin')->value('id');
        $assign($adminId, ['Order History', 'Room History']);
    }

    public function down(): void
    {
        $roomStaffId = DB::table('roles')->where('name', 'Room Staff')->value('id');

        // Remove the modules this migration granted to Room Staff (keep Room Management — added earlier).
        $revokeNames = ['Shifts & Till Management', 'Reports', 'Order History', 'Room History', 'Settings'];
        $revokeIds = DB::table('modules')->whereIn('name', $revokeNames)->pluck('id');
        if ($roomStaffId) {
            DB::table('role_module')->where('role_id', $roomStaffId)->whereIn('module_id', $revokeIds)->delete();
        }

        // Remove the standalone history modules entirely (and all their role links).
        $histIds = DB::table('modules')->whereIn('name', ['Order History', 'Room History'])->pluck('id');
        DB::table('role_module')->whereIn('module_id', $histIds)->delete();
        DB::table('modules')->whereIn('id', $histIds)->delete();
    }
};
