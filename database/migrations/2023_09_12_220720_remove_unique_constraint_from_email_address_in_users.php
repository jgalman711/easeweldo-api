<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_email_unique');
            });
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email_address');
        });
    }
};
