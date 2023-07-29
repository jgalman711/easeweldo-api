<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('regular_holiday_hours_worked_pay', 10, 2)->after('regular_holiday_hours_worked');
            $table->decimal('regular_holiday_hours_worked_pay_ytd', 10, 2)->after('regular_holiday_hours_worked_pay');
            $table->decimal('special_holiday_hours_worked_pay', 10, 2)->after('special_holiday_hours_worked');
            $table->decimal('special_holiday_hours_worked_pay_ytd', 10, 2)->after('special_holiday_hours_worked_pay');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'regular_holiday_hours_worked_pay',
                'regular_holiday_hours_worked_pay_ytd',
                'special_holiday_hours_worked_pay',
                'special_holiday_hours_worked_pay_ytd',
            ]);
        });
    }
};
