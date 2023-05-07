<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagibig', function (Blueprint $table) {
            $table->id();
            $table->double('min_compensation');
            $table->double('max_compensation');
            $table->double('employee_contribution_rate');
            $table->double('employer_contribution_rate');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagibig');
    }
};
