<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->index('employee_id');
            $table->index('period_id');
            $table->index('deleted_at');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('company_id');
            $table->index('deleted_at');
        });
    }
};
