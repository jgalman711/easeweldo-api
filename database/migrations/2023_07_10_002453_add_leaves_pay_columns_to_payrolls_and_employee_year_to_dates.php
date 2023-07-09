<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('leaves_pay', 8, 2)->after('leaves')->default(0);
            $table->decimal('leaves_pay_ytd', 8, 2)->after('leaves_pay')->default(0);
        });

        Schema::table('employee_year_to_dates', function (Blueprint $table) {
            $table->decimal('leaves_pay', 8, 2)->after('undertime_deductions')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('leaves_pay');
            $table->dropColumn('leaves_pay_ytd');
        });

        Schema::table('employee_year_to_dates', function (Blueprint $table) {
            $table->dropColumn('leaves_pay');
        });
    }
};
