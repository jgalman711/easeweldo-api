<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('auto_pay_disbursement')->default(false)->after('auto_send_email_to_bank');
            $table->boolean('clock_action_required')->default(false)->after('auto_pay_disbursement');
            $table->string('disbursement_method')->nullable()->after('clock_action_required');
            $table->decimal('overtime_rate', 8, 2)->default(1)->after('disbursement_method');
            $table->boolean('leaves_convertible')->default(false)->after('overtime_rate');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('auto_send_email_to_bank');
            $table->dropColumn('auto_pay_disbursement');
            $table->dropColumn('clock_action_required');
            $table->dropColumn('disbursement_method');
            $table->dropColumn('overtime_rate');
            $table->dropColumn('leaves_convertible');
        });
    }
};
