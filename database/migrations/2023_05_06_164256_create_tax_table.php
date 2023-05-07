<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['weekly', 'semi-monthly', 'monthly']);
            $table->double('min_compensation');
            $table->double('max_compensation');
            $table->double('base_tax');
            $table->double('over_compensation_level_rate');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxes');
    }
};
