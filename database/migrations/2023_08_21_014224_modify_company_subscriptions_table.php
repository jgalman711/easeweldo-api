<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->decimal('amount_per_employee', 10, 2)->default(0)->after('status');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('amount');
            $table->decimal('balance', 10, 2)->default(0)->after('amount_paid');
            $table->decimal('overpaid_balance', 10, 2)->default(0)->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('company_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'balance', 'amount_per_employee, overpaid_balance']);
        });
    }
};
