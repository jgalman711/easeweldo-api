<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'regular_holiday_hours_worked',
                'regular_holiday_hours_worked_pay',
                'regular_holiday_hours',
                'regular_holiday_hours_pay',
                'special_holiday_hours_worked',
                'special_holiday_hours_worked_pay',
                'special_holiday_hours',
                'special_holiday_hours_pay',
                'total_contributions',
                'gross_income',
                'taxable_income',
                'net_income',
            ]);
            
            $table->json('holidays')->after('non_taxable_earnings')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('regular_holiday_hours_worked', 8, 2)->default(0);
            $table->decimal('regular_holiday_hours_worked_pay', 14, 2)->default(0);
            $table->decimal('regular_holiday_hours', 8, 2)->default(0);
            $table->decimal('regular_holiday_hours_pay', 14, 2)->default(0);
            $table->decimal('special_holiday_hours_worked', 8, 2)->default(0);
            $table->decimal('special_holiday_hours_worked_pay', 14, 2)->default(0);
            $table->decimal('special_holiday_hours', 8, 2)->default(0);
            $table->decimal('special_holiday_hours_pay', 14, 2)->default(0);
            $table->decimal('total_contributions', 14, 2)->default(0);
            $table->decimal('gross_income', 14, 2)->default(0);
            $table->decimal('taxable_income', 14, 2)->default(0);
            $table->decimal('net_income', 14, 2)->default(0);

            $table->dropColumn('holidays');
        });
    }
};
