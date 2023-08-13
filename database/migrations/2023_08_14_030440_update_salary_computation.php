<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->dropColumn([
                'allowances',
                'commissions',
                'other_compensations',
            ]);

            $table->json('taxable_earnings')->nullable()->after('non_taxable_earnings');
        });
    }

    public function down()
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->json('allowances')->after('daily_rate')->nullable();
            $table->json('commissions')->after('allowances')->nullable();
            $table->json('other_compensations')->after('commissions')->nullable();

            $table->dropColumn('taxable_earnings');
        });
    }
};
