<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('income_tax', 'withheld_tax');
            $table->renameColumn('income_tax_ytd', 'withheld_tax_ytd');

            $table->dropColumn('taxable_income_ytd');

            $table->decimal('total_allowances')->after('allowances')->default(0);
            $table->decimal('total_commissions')->after('commissions')->default(0);
            $table->decimal('total_other_compensations')->after('other_compensations')->default(0);
            $table->decimal('total_allowances_ytd')->after('total_allowances')->default(0);
            $table->decimal('total_commissions_ytd')->after('total_commissions')->default(0);
            $table->decimal('total_other_compensations_ytd')->after('total_other_compensations')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('withheld_tax', 'income_tax');
            $table->renameColumn('withheld_tax_ytd', 'income_tax_ytd');
            $table->decimal('taxable_income_ytd')->after('taxable_income')->default(0);
            
            $table->dropColumn([
                'total_allowances',
                'total_commissions',
                'total_other_compensations',
                'total_allowances_ytd',
                'total_commissions_ytd',
                'total_other_compensations_ytd',
            ]);
        });
    }
};
