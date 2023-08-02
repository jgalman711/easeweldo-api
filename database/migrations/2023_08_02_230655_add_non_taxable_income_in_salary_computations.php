<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->json('non_taxable_earnings')->after('other_compensations')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->dropColumn('non_taxable_earnings');
        });
    }
};
