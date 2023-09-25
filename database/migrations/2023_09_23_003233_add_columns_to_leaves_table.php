x<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->string('description')->after('type')->nullable();
            $table->unsignedBigInteger('approved_by')->after('description')->nullable();
            $table->timestamp('approved_date')->after('approved_by')->nullable();
            $table->timestamp('submitted_date')->after('approved_date')->nullable();
            $table->text('remarks')->after('submitted_date')->nullable();
            
            // rename start_date and end_date to from_date, to_date
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');

            $table->date('from_date')->after('description')->nullable();
            $table->date('to_date')->after('from_date')->nullable();
            // end
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_date');
            $table->dropColumn('submitted_date');
            $table->dropColumn('remarks');

            // rename start_date and end_date to from_date, to_date
            $table->dropColumn('from_date');
            $table->dropColumn('to_date');

            $table->date('start_date')->after('description');
            $table->date('end_date')->after('from_date');
             // end
        });
    }
};
