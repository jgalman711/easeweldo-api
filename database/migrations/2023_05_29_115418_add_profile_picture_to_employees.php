<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('contact_number');
            $table->string('mobile_number')->after('employment_status')->nullable();
            $table->string('profile_picture')->after('bank_account_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('contact_number')->after('bank_account_number')->nullable();
            $table->dropColumn('profile_picture');
            $table->dropColumn('mobile_number');
        });
    }
};
