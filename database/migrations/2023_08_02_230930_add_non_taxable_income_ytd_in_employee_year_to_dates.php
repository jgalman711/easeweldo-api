<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_year_to_dates', function (Blueprint $table) {
            $table->decimal('total_non_taxable_earnings', 10, 2)->after('total_compensations')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('employee_year_to_dates', function (Blueprint $table) {
            $table->dropColumn('non_taxable_earnings');
        });
    }
};
