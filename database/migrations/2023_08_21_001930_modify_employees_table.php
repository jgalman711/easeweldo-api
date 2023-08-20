<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('employment_type', Employee::EMPLOYMENT_TYPE)->change();
            $table->enum('employment_status', Employee::EMPLOYMENT_STATUS)->change();
            $table->enum('status', Employee::STATUS)->after('job_title');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employment_type')->change();
            $table->string('employment_status')->change();
            $table->dropColumn('status');
        });
    }
};
