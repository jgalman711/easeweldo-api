<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'add employee']);
        Permission::firstOrCreate(['name' => 'edit employee']);
        Permission::firstOrCreate(['name' => 'delete employee']);
        Permission::firstOrCreate(['name' => 'view employee']);
        Permission::firstOrCreate(['name' => 'view employees']);
        Permission::firstOrCreate(['name' => 'add company']);
        Permission::firstOrCreate(['name' => 'edit company']);
        Permission::firstOrCreate(['name' => 'delete company']);
        Permission::firstOrCreate(['name' => 'view company']);
        Permission::firstOrCreate(['name' => 'view companies']);

        $businessAdmin = Role::firstOrCreate(['name' => 'business-admin']);
        $businessAdmin->givePermissionTo([
            'view company',
            'edit company',
            'add employee',
            'edit employee',
            'delete employee',
            'view employee',
            'view employees'
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());
    }
}
