<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_records', function (Blueprint $table) {
            $table->text('remarks')->after('clock_out')->nullable();
            $table->string('attendance_status')->after('clock_out')->nullable();
            $table->time('expected_clock_out')->after('clock_out')->nullable();
            $table->time('expected_clock_in')->after('clock_out')->nullable();
            $table->dateTime('clock_in')->nullable()->change();
            $table->dateTime('clock_out')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('time_records', function (Blueprint $table) {
            $table->dropColumn(['expected_clock_in', 'expected_clock_out', 'attendance_status', 'remarks']);
        });
    }
};
