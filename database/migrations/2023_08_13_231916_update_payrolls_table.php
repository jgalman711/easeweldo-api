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
                'allowances',
                'total_allowances',
                'total_allowances_ytd',
                'commissions',
                'total_commissions',
                'total_commissions_ytd',
                'other_compensations',
                'total_other_compensations',
                'total_other_compensations_ytd',
                'total_non_taxable_earnings',
                'total_non_taxable_earnings_ytd',
                'overtime_pay_ytd',
                'late_deductions_ytd',
                'absent_deductions_ytd',
                'undertime_deductions_ytd',
                'leaves_pay_ytd',
                'regular_holiday_hours_worked_pay_ytd',
                'regular_holiday_hours_pay_ytd',
                'special_holiday_hours_worked_pay_ytd',
                'special_holiday_hours_pay_ytd',
                'sss_contributions_ytd',
                'philhealth_contributions_ytd',
                'pagibig_contributions_ytd',
                'total_contributions_ytd',
                'gross_income_ytd',
                'withheld_tax_ytd',
                'net_income_ytd'
            ]);

            $table->json('taxable_earnings')->after('leaves_pay')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'taxable_earnings'
            ]);
        });
    }
};
