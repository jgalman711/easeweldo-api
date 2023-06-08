<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->unsignedBigInteger('period_id')->nullable()->change();
            $table->string('description')->nullable()->after('period_id');
            $table->string('remarks')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->unsignedBigInteger('period_id')->change();
            $table->dropColumn('description');
            $table->dropColumn('remarks');
        });
    }
};
