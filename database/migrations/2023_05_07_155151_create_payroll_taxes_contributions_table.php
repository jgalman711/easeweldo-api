<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_taxes_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_id');
            $table->unsignedBigInteger('company_id');
            $table->decimal('withholding_tax', 10, 2);
            $table->decimal('sss_contribution', 10, 2);
            $table->decimal('pagibig_contribution', 10, 2);
            $table->enum('status', ['processing', 'completed', 'cancelled'])->default('processing');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('payroll_id')->references('id')->on('payrolls');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_taxes_contributions');
    }
};
