<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('working_days_per_week');
            $table->dropColumn('working_hours_per_day');
        });

        Schema::table('salary_computations', function (Blueprint $table) {
            $table->integer('working_days_per_week')->after('daily_rate')->nullable();
            $table->integer('working_hours_per_day')->after('daily_rate')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('working_days_per_week')->after('profile_picture')->nullable();
            $table->integer('working_hours_per_day')->after('profile_picture')->nullable();
        });
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->dropColumn('working_days_per_week');
            $table->dropColumn('working_hours_per_day');
        });
    }
};
