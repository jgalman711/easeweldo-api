<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payrolls');

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('period_id');
            $table->decimal('basic_salary', 8, 2);
            $table->decimal('total_late_minutes', 8, 2);
            $table->decimal('total_late_deductions', 8, 2);
            $table->decimal('total_absent_days', 8, 2);
            $table->decimal('total_absent_deductions', 8, 2);
            $table->decimal('total_overtime_minutes', 8, 2);
            $table->decimal('total_overtime_pay', 8, 2);
            $table->decimal('total_undertime_minutes', 8, 2);
            $table->decimal('total_undertime_deductions', 8, 2);
            $table->decimal('sss_contribution', 8, 2);
            $table->decimal('philhealth_contribution', 8, 2);
            $table->decimal('pagibig_contribution', 8, 2);
            $table->decimal('total_contributions', 8, 2);
            $table->decimal('taxable_income', 8, 2);
            $table->decimal('base_tax', 8, 2);
            $table->decimal('compensation_level', 8, 2);
            $table->decimal('tax_rate', 8, 2);
            $table->decimal('income_tax', 8, 2);
            $table->decimal('net_salary', 8, 2);
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('period_id')->references('id')->on('periods');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
