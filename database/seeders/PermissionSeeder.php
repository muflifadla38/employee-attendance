<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $employeeRole = Role::where('name', 'employee')->first();

        $types = ['read', 'create', 'update', 'delete'];
        $features = ['profile', 'user', 'employee'];
        $permissions = [];

        foreach ($features as $feature) {
            foreach ($types as $type) {
                $permissionName = "$type $feature";

                Permission::create([
                    'name' => $permissionName,
                ]);

                $permissions['admin'][] = $permissionName;

                if ($feature == 'profile' && in_array($type, ['read', 'create'])) {
                    $permissions['employee'][] = $permissionName;
                }
            }
        }

        $adminRole->givePermissionTo($permissions['admin']);
        $employeeRole->givePermissionTo($permissions['employee']);
    }
}
