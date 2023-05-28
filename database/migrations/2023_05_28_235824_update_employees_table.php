<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('employees', 'work_arrangement')) {
                $table->dropColumn('work_arrangement');
            }
            $table->string('bank_name')->after('tax_identification_number')->nullable();
            $table->string('bank_account_name')->after('bank_name')->nullable();
            $table->string('employment_status')->after('job_title')->nullable();
            $table->string('address_line')->after('employment_status')->nullable();
            $table->string('barangay_town_city_province')->after('address_line')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('work_arrangement')->nullable();
            $table->dropColumn('address_line');
            $table->dropColumn('barangay_town_city_province');
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_account_name');
            $table->dropColumn('employment_status');
        });
    }
};
