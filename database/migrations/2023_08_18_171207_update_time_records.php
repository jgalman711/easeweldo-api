<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_records', function (Blueprint $table) {
            $table->timestamp('original_clock_in')->nullable()->after('expected_clock_out');
            $table->timestamp('original_clock_out')->nullable()->after('original_clock_in');
        });

        if (Schema::hasColumn('time_records', 'attendance_status')) {
            Schema::table('time_records', function (Blueprint $table) {
                $table->dropColumn('attendance_status');
            });
        }

        Schema::table('time_records', function (Blueprint $table) {
            $table->string('source')->after('original_clock_out')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('time_records', function (Blueprint $table) {
            $table->dropColumn('original_clock_in');
            $table->dropColumn('original_clock_out');
        });

        Schema::table('time_records', function (Blueprint $table) {
            $table->string('attendance_status')->after('expected_clock_out')->nullable();
        });

        if (Schema::hasColumn('time_records', 'source')) {
            Schema::table('time_records', function (Blueprint $table) {
                $table->dropColumn('source');
            });
        }
    }
};
