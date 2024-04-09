<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::create(['name' => 'leave-approver']);
    }

    public function down(): void
    {
        Role::where(['name' => 'leave-approver'])->delete();
    }
};
