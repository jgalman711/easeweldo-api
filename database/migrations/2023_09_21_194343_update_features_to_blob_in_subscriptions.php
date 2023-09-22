<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function ($table) {
            $table->dropColumn('feature');
            $table->binary('feature')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function ($table) {
            $table->dropColumn('feature');
            $table->json('feature')->nullable();
        });
    }
};
