<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePeriodsTable extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->renameColumn('company_period_number', 'company_period_id');

            $table->decimal('payroll_cost', 10, 2)->after('company_period_number')->default(0);
            $table->integer('employees_count')->after('payroll_cost')->default(0);
            $table->decimal('employees_net_pay', 10, 2)->after('employees_count')->default(0);
            $table->decimal('withheld_taxes', 10, 2)->after('employees_net_pay')->default(0);
            $table->decimal('total_contributions', 10, 2)->after('withheld_taxes')->default(0);
            $table->text('description')->after('total_contributions')->nullable();
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->renameColumn('company_period_id', 'company_period_number');
            $table->dropColumn([
                'payroll_cost',
                'employees_count',
                'employees_net_pay',
                'withheld_taxes',
                'total_contributions',
                'description',
            ]);
        });
    }
}