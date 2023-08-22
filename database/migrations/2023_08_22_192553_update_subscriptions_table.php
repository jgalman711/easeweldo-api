<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('type', ['core', 'add-on'])->after('name');
            $table->text('title')->after('type');
            $table->text('description')->after('title');
            $table->json('features')->after('description');
            $table->decimal('discount', 10, 2)->default(0)->after('amount');
            $table->dropColumn('details');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('title');
            $table->dropColumn('features');
            $table->dropColumn('description');
            $table->dropColumn('discount');
            $table->text('details')->after('name');
        });
    }
};
