<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'payroll_cost',
                'employees_count',
                'employees_net_pay',
                'withheld_taxes',
                'total_contributions',
            ]);
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->decimal('payroll_cost', 10, 2)->default(0.00);
            $table->unsignedInteger('employees_count')->default(0);
            $table->decimal('employees_net_pay', 10, 2)->default(0.00);
            $table->decimal('withheld_taxes', 10, 2)->default(0.00);
            $table->decimal('total_contributions', 10, 2)->default(0.00);
        });
    }
};
