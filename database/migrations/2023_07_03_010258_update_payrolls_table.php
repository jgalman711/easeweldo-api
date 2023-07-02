<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('hours_worked', 8, 2)->nullable()->change();
            $table->decimal('expected_hours_worked', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('hours_worked')->nullable()->change();
            $table->integer('expected_hours_worked')->nullable()->change();
        });
    }
};
