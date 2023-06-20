<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn('regular_holiday_minutes_pay');
            $table->dropColumn('regular_holiday_worked_minutes');
            $table->dropColumn('regular_holiday_worked_minutes_pay');
            $table->dropColumn('regular_holiday_worked_minutes_pay_ytd');

            $table->dropColumn('special_holiday_minutes_pay');
            $table->dropColumn('special_holiday_worked_minutes');
            $table->dropColumn('special_holiday_worked_minutes_pay');
            $table->dropColumn('special_holiday_worked_minutes_pay_ytd');

            // Add new columns
            $table->decimal('regular_holiday_hours', 10, 2)->after('other_compensations')->nullable();
            $table->decimal('regular_holiday_hours_worked', 10, 2)->after('other_compensations')->nullable();
            $table->decimal('regular_holiday_hours_pay', 10, 2)->after('regular_holiday_hours')->nullable();
            $table->decimal('regular_holiday_hours_pay_ytd', 10, 2)->after('regular_holiday_hours_pay')->nullable();
            
            $table->decimal('special_holiday_hours', 10, 2)->after('regular_holiday_hours_pay_ytd')->nullable();
            $table->decimal('special_holiday_hours_worked', 10, 2)->after('regular_holiday_hours_pay_ytd')->nullable();
            $table->decimal('special_holiday_hours_pay', 10, 2)->after('special_holiday_hours')->nullable();
            $table->decimal('special_holiday_hours_pay_ytd', 10, 2)->after('special_holiday_hours_pay')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn('regular_holiday_hours');
            $table->dropColumn('regular_holiday_hours_worked');
            $table->dropColumn('regular_holiday_hours_pay');
            $table->dropColumn('regular_holiday_hours_pay_ytd');

            $table->dropColumn('special_holiday_hours');
            $table->dropColumn('special_holiday_hours_worked');
            $table->dropColumn('special_holiday_hours_pay');
            $table->dropColumn('special_holiday_hours_pay_ytd');

            // Add back old columns
            $table->integer('regular_holiday_minutes_pay')->after('other_compensations')->nullable();
            $table->integer('regular_holiday_worked_minutes')->after('regular_holiday_minutes_pay')->nullable();
            $table->decimal('regular_holiday_worked_minutes_pay', 10, 2)->after('regular_holiday_worked_minutes')->nullable();
            $table->decimal('regular_holiday_worked_minutes_pay_ytd', 10, 2)->after('regular_holiday_worked_minutes_pay')->nullable();

            $table->decimal('special_holiday_minutes_pay', 10, 2)->after('regular_holiday_worked_minutes_pay_ytd')->nullable();
            $table->integer('special_holiday_worked_minutes')->after('special_holiday_minutes_pay')->nullable();
            $table->decimal('special_holiday_worked_minutes_pay', 10, 2)->after('special_holiday_worked_minutes')->nullable();
            $table->decimal('special_holiday_worked_minutes_pay_ytd', 10, 2)->after('special_holiday_worked_minutes_pay')->nullable();
        });
    }
};
