<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'business-admin']);
        Role::firstOrCreate(['name' => 'approver']);
        Role::firstOrCreate(['name' => 'employee']);
    }
}
