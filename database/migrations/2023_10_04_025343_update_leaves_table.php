<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['from_date', 'to_date']);
            $table->date('date')->after('description');
            $table->decimal('hours', 8, 2)->after('description');
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['date', 'hours']);
            $table->dateTime('from_date')->nullable()->after('description');
            $table->dateTime('to_date')->nullable()->after('from_date');
        });
    }
};
