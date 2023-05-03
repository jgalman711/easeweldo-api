<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade')->nullable();
            $table->string('name');
            $table->time('monday_clock_in_time')->nullable();
            $table->time('monday_clock_out_time')->nullable();
            $table->time('tuesday_clock_in_time')->nullable();
            $table->time('tuesday_clock_out_time')->nullable();
            $table->time('wednesday_clock_in_time')->nullable();
            $table->time('wednesday_clock_out_time')->nullable();
            $table->time('thursday_clock_in_time')->nullable();
            $table->time('thursday_clock_out_time')->nullable();
            $table->time('friday_clock_in_time')->nullable();
            $table->time('friday_clock_out_time')->nullable();
            $table->time('saturday_clock_in_time')->nullable();
            $table->time('saturday_clock_out_time')->nullable();
            $table->time('sunday_clock_in_time')->nullable();
            $table->time('sunday_clock_out_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
