<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'hours_worked',
                'expected_hours_worked',
                'overtime_minutes',
                'overtime_pay',
                'late_minutes',
                'late_deductions',
                'absent_minutes',
                'absent_deductions',
                'undertime_minutes',
                'undertime_deductions',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->float('hours_worked')->nullable();
            $table->float('expected_hours_worked')->nullable();
            $table->integer('overtime_minutes')->nullable();
            $table->decimal('overtime_pay', 10, 2)->nullable();
            $table->integer('late_minutes')->nullable();
            $table->decimal('late_deductions', 10, 2)->nullable();
            $table->integer('absent_minutes')->nullable();
            $table->decimal('absent_deductions', 10, 2)->nullable();
            $table->integer('undertime_minutes')->nullable();
            $table->decimal('undertime_deductions', 10, 2)->nullable();
        });
    }
};
