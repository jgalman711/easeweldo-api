<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('period_cycle')->nullable()->change();
            $table->text('salary_day')->nullable()->change();
            $table->integer('grace_period')->nullable()->change();
            $table->integer('minimum_overtime')->nullable()->change();
        });
    }
};
