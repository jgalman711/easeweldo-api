<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->date('due_date')->after('logo')->default(Carbon::now());
            $table->decimal('amount_due')->after('logo')->default(200);
            $table->enum('subscription_status', ['unpaid', 'paid'])->after('logo');
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('due_date');
            $table->dropColumn('amount_due');
            $table->dropColumn('subscription_status');
        });
    }
};
