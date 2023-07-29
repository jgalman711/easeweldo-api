<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employee_year_to_dates', function (Blueprint $table) {
            $table->decimal('regular_holiday_hours_worked_pay', 10, 2)->after('regular_holiday_hours_pay');
            $table->decimal('special_holiday_hours_worked_pay', 10, 2)->after('special_holiday_hours_pay');
        });
    }

    public function down()
    {
        Schema::table('employee_year_to_dates', function (Blueprint $table) {
            $table->dropColumn([
                'regular_holiday_hours_worked_pay',
                'special_holiday_hours_worked_pay',
            ]);
        });
    }
};
