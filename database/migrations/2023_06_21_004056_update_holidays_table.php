<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->string('type')->change();
            $table->text('description')->after('name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
