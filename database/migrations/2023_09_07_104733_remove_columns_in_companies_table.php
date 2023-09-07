<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'subscription_status',
            'amount_due',
            'due_date'
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('companies', $column)) {
                Schema::table('companies', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
