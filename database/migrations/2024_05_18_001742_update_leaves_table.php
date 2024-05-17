<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_date']);
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by')->after('submitted_date')->nullable();
            $table->timestamp('approved_date')->after('approved_by')->nullable();
        });
    }
};
