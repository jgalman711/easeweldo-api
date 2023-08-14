<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
            $table->json('subscriptions')->after('company_id');
        });
    }

    public function down()
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->dropColumn('subscriptions');
            $table->unsignedBigInteger('subscription_id')->after('company_id');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
        });
    }
};
