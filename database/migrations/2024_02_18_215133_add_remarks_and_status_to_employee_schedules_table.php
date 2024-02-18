<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('flexi_hours_required');
            $table->enum('status', ['inactive', 'active', 'upcoming'])->default('upcoming')->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('status');
        });
    }
};
