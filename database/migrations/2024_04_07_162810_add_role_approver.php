<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public const APPROVER = 'approver';
    public const PERMISSIONS = [
        'approve leaves',
        'approve overtimes'
    ];

    public function up(): void
    {
        $role = Role::firstOrCreate(['name' => self::APPROVER]);
        $permissions = [];
        foreach (self::PERMISSIONS as $permission) {
            $permissions[] = Permission::firstOrCreate(['name' => $permission]);
        }
        $role->syncPermissions($permissions);
    }

    public function down(): void
    {
        $role = Role::where(['name' => self::APPROVER])->first();
        $role->revokePermissionTo(self::PERMISSIONS);
    }
};
