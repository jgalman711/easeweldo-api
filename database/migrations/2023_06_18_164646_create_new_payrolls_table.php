<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payrolls');

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('period_id')->nullable();
            $table->string('description')->nullable();
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->integer('hours_worked')->nullable();
            $table->integer('expected_hours_worked')->nullable();
            $table->integer('overtime_minutes')->nullable();
            $table->decimal('overtime_pay', 10, 2)->nullable();
            $table->decimal('overtime_pay_ytd', 10, 2)->nullable();
            $table->integer('late_minutes')->nullable();
            $table->decimal('late_deductions', 10, 2)->nullable();
            $table->decimal('late_deductions_ytd', 10, 2)->nullable();
            $table->integer('absent_minutes')->nullable();
            $table->decimal('absent_deductions', 10, 2)->nullable();
            $table->decimal('absent_deductions_ytd', 10, 2)->nullable();
            $table->integer('undertime_minutes')->nullable();
            $table->decimal('undertime_deductions', 10, 2)->nullable();
            $table->decimal('undertime_deductions_ytd', 10, 2)->nullable();
            $table->json('leaves')->nullable();
            $table->json('allowances')->nullable();
            $table->json('commissions')->nullable();
            $table->json('other_compensations')->nullable();
            $table->decimal('regular_holiday_minutes_pay', 10, 2)->nullable();
            $table->integer('regular_holiday_worked_minutes')->nullable();
            $table->decimal('regular_holiday_worked_minutes_pay', 10, 2)->nullable();
            $table->decimal('regular_holiday_worked_minutes_pay_ytd', 10, 2)->nullable();
            $table->decimal('special_holiday_minutes_pay', 10, 2)->nullable();
            $table->integer('special_holiday_worked_minutes')->nullable();
            $table->decimal('special_holiday_worked_minutes_pay', 10, 2)->nullable();
            $table->decimal('special_holiday_worked_minutes_pay_ytd', 10, 2)->nullable();
            $table->decimal('sss_contributions', 10, 2)->nullable();
            $table->decimal('sss_contributions_ytd', 10, 2)->nullable();
            $table->decimal('philhealth_contributions', 10, 2)->nullable();
            $table->decimal('philhealth_contributions_ytd', 10, 2)->nullable();
            $table->decimal('pagibig_contributions', 10, 2)->nullable();
            $table->decimal('pagibig_contributions_ytd', 10, 2)->nullable();
            $table->decimal('total_contributions', 10, 2)->nullable();
            $table->decimal('total_contributions_ytd', 10, 2)->nullable();
            $table->decimal('gross_income', 10, 2)->nullable();
            $table->decimal('gross_income_ytd', 10, 2)->nullable();
            $table->decimal('taxable_income', 10, 2)->nullable();
            $table->decimal('taxable_income_ytd', 10, 2)->nullable();
            $table->decimal('income_tax', 10, 2)->nullable();
            $table->decimal('income_tax_ytd', 10, 2)->nullable();
            $table->decimal('net_income', 10, 2)->nullable();
            $table->decimal('net_income_ytd', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('period_id')->references('id')->on('periods');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payrolls');
    }
};
