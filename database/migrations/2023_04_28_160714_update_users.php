<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('mobile_number')->after('id')->nullable();
            $table->unsignedBigInteger('employee_id')->after('mobile_number')->nullable();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->dropColumn('mobile_number');
            $table->dropSoftDeletes();
            $table->dropColumn('employee_id');
        });
    }
};
