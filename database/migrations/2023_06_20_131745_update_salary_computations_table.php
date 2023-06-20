<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->json('other_compensations')->after('daily_rate')->nullable();
            $table->json('commissions')->after('daily_rate')->nullable();
            $table->json('allowances')->after('daily_rate')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->dropColumn('allowances');
            $table->dropColumn('commissions');
            $table->dropColumn('other_compensations');
        });
    }
};
