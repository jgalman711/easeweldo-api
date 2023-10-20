<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->boolean('is_clock_required')->after('start_date')->default(true);
            $table->boolean('flexi_hours_required')->after('is_clock_required')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropColumn('is_clock_required');
            $table->dropColumn('flexi_hours_required');
        });
    }
};
