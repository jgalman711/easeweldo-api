<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('legal_name')->after('logo')->nullable();
            $table->string('address_line')->after('legal_name')->nullable();
            $table->string('barangay_town_city_province')->after('address_line_2')->nullable();
            $table->string('contact_name')->after('barangay_town_city_province')->nullable();
            $table->string('email_address')->after('contact_name')->nullable();
            $table->string('mobile_number')->after('email_address')->nullable();
            $table->string('landline_number')->after('mobile_number')->nullable();
            $table->string('bank_name')->after('landline_number')->nullable();
            $table->string('bank_account_name')->after('bank_name')->nullable();
            $table->string('bank_account_number')->after('bank_account_name')->nullable();
            $table->string('tin')->after('bank_account_number')->nullable();
            $table->string('sss_number')->after('tin')->nullable();
            $table->string('philhealth_number')->after('sss_number')->nullable();
            $table->string('pagibig_number')->after('philhealth_number')->nullable();

            $table->dropColumn('due_date');
            $table->dropColumn('amount_due');
            $table->dropColumn('subscription_status');
        });
        
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'legal_name',
                'address_line',
                'barangay_town_city_province',
                'contact_name',
                'email_address',
                'mobile_number',
                'landline_number',
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'tin',
                'sss_number',
                'philhealth_number',
                'pagibig_number',
            ]);
            $table->date('due_date')->after('logo')->default(Carbon::now());
            $table->decimal('amount_due')->after('logo')->default(200);
            $table->enum('subscription_status', ['unpaid', 'paid'])->after('logo');
        });
    }
};
