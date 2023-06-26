<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_year_to_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->decimal('net_income', 10, 2)->default(0);
            $table->decimal('gross_income', 10, 2)->default(0);
            $table->decimal('withheld_tax', 10, 2)->default(0);
            $table->decimal('total_contributions', 10, 2)->default(0);
            $table->decimal('total_commissions', 10, 2)->default(0);
            $table->decimal('total_allowances', 10, 2)->default(0);
            $table->decimal('total_compensations', 10, 2)->default(0);
            $table->decimal('sss_contributions', 10, 2)->default(0);
            $table->decimal('philhealth_contributions', 10, 2)->default(0);
            $table->decimal('pagibig_contributions', 10, 2)->default(0);
            $table->decimal('regular_holiday_hours_pay', 10, 2)->default(0);
            $table->decimal('special_holiday_hours_pay', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('late_deductions', 10, 2)->default(0);
            $table->decimal('absent_deductions', 10, 2)->default(0);
            $table->decimal('undertime_deductions', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_year_to_dates');
    }
};
