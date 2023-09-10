<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('username')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
        });

        DB::table('employees')
            ->join('users', 'employees.id', '=', 'users.employee_id')
            ->update([
                'users.first_name' => DB::raw('employees.first_name'),
                'users.last_name' => DB::raw('employees.last_name'),
            ]);

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name')->after('company_employee_id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
        });

        DB::table('users')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->update([
                'employees.first_name' => DB::raw('users.first_name'),
                'employees.last_name' => DB::raw('users.last_name'),
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
