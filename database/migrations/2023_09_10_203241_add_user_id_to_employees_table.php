<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_employee_id_foreign');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        DB::table('employees')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->update(['employees.user_id' => DB::raw('users.id')]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
    }
};
