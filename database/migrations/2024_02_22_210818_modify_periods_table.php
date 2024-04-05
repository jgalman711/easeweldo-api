<?php

use App\Models\Period;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('periods', function (Blueprint $table) {
            $table->enum('type', Period::TYPES)->after('description');
            $table->enum('subtype', Period::SUBTYPES)->after('type')->nullable();
        });
    }
};
