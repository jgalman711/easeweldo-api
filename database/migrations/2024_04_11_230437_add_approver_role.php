<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        try {
            $users = User::role('leave-approver')->get();
            if ($users) {
                foreach ($users as $user) {
                    $user->removeRole('leave-approver');
                }
            }
            Role::where(['name' => 'leave-approver'])->delete();
        } catch (RoleDoesNotExist $e) {
            Log::error($e->getMessage());
        }

        $permissions = [
            'approve leave',
            'approve overtime',
            'approve time-correction',
            'decline leave',
            'decline overtime',
            'decline time-correction'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
      
        $approver = Role::firstOrCreate(['name' => 'approver']);
        $approver->givePermissionTo($permissions);
    }
};
