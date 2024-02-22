<?php

use App\Enumerators\PayrollEnumerator;
use App\Models\Period;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->enum('status', PayrollEnumerator::STATUSES)->change();
        });

        Schema::table('periods', function (Blueprint $table) {
            $table->enum('status', Period::STATUSES)->change();
        });
    }
};
