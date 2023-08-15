<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->unsignedDecimal('regular_holiday_hours_worked')->default(0)->change();
            $table->decimal('regular_holiday_hours_worked_pay', 10, 2)->default(0)->change();
            $table->unsignedDecimal('regular_holiday_hours')->default(0)->change();
            $table->decimal('regular_holiday_hours_pay', 10, 2)->default(0)->change();
            $table->unsignedDecimal('special_holiday_hours_worked')->default(0)->change();
            $table->decimal('special_holiday_hours_worked_pay', 10, 2)->default(0)->change();
            $table->unsignedDecimal('special_holiday_hours')->default(0)->change();
            $table->decimal('special_holiday_hours_pay', 10, 2)->default(0)->change();
        });
    }
};
