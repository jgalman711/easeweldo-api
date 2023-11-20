<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->nullable()->change();
            $table->string('job_title')->nullable()->change();
            $table->string('date_of_hire')->nullable()->change();
            $table->string('date_of_birth')->nullable()->change();
            $table->string('bank_account_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->nullable(false)->change();
            $table->string('job_title')->nullable(false)->change();
            $table->string('date_of_hire')->nullable(false)->change();
            $table->string('date_of_birth')->nullable(false)->change();
            $table->string('bank_account_number')->nullable(false)->change();
        });
    }
};
