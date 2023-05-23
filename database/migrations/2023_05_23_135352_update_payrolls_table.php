<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('total_leave_hours', 8, 2)->after('total_undertime_deductions')->default(0);
            $table->decimal('total_leave_compensation', 10, 2)->after('total_leave_hours')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('total_leave_compensation');
            $table->dropColumn('total_leave_hours');
        });
    }
};
