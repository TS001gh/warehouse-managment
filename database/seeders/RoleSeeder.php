<?php

namespace Database\Seeders;

use App\Enums\Permissions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Create Admin Role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permissions::cases());

        // Create Employee Role and assign specific permissions
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeePermissions = [
            Permissions::VIEW_ITEMS->value,
            Permissions::ADD_ITEM->value,
            Permissions::EDIT_ITEM->value,
        ];
        $employeeRole->syncPermissions($employeePermissions);
    }
}
