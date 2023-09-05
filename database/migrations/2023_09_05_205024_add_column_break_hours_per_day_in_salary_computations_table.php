<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->integer('break_hours_per_day')->after('working_hours_per_day')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->dropColumn('break_hours_per_day');
        });
    }
};
