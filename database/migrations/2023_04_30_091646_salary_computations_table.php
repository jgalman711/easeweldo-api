<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_computations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->float('basic_salary');
            $table->float('overtime_rate');
            $table->float('night_diff_rate');
            $table->float('regular_holiday_rate');
            $table->float('special_holiday_rate');
            $table->double('total_sick_leaves');
            $table->double('total_vacation_leaves');
            $table->double('available_sick_leaves');
            $table->double('available_vacation_leaves');
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_computations');
    }
};
