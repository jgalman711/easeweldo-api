<?php

use App\Enumerators\LeaveEnumerator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('status', LeaveEnumerator::STATUSES)->after('remarks');
        });
    }
};
