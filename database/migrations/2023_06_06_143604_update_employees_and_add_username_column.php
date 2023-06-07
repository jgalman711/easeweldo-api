<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('sss_number')->nullable()->change();
            $table->string('pagibig_number')->nullable()->change();
            $table->string('philhealth_number')->nullable()->change();
            $table->string('tax_identification_number')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('email')->nullable();
            $table->dropColumn('mobile_number');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('mobile_number')->after('email')->nullable();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('sss_number')->nullable(false)->change();
            $table->string('pagibig_number')->nullable(false)->change();
            $table->string('philhealth_number')->nullable(false)->change();
            $table->string('tax_identification_number')->nullable(false)->change();
        });
    }
};
