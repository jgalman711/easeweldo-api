<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('department');
            $table->string('job_title');
            $table->date('date_of_hire');
            $table->date('date_of_birth');
            $table->string('contact_number');
            $table->string('social_security_number')->unique();
            $table->string('address');
            $table->string('bank_account_number');
            $table->decimal('pay_rate', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
        Schema::dropIfExists('employees');
    }
};
