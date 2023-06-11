<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('total_hours_worked', 8, 2)->default(0)->after('basic_salary');
            $table->decimal('total_expected_hours_worked', 8, 2)->default(0)->after('basic_salary');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('total_hours_worked');
            $table->dropColumn('total_expected_hours_worked');
        });
    }
};
