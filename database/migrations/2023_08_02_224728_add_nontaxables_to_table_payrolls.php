<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->json('non_taxable_earnings', 10, 2)->after('total_other_compensations_ytd')->nullable();
            $table->decimal('total_non_taxable_earnings', 10, 2)->after('non_taxable_earnings')->nullable();
            $table->decimal('total_non_taxable_earnings_ytd', 10, 2)->after('total_non_taxable_earnings')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'non_taxable_earnings',
                'total_non_taxable_earnings',
                'total_non_taxable_earnings_ytd'
            ]);
        });
    }
};
