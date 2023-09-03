<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->integer('employee_count')->after('amount_per_employee');
        });
    }

    public function down()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->dropColumn('employee_count');
        });
    }
};
