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
            $table->enum('employment_type', Employee::EMPLOYMENT_TYPE)->after('employment_status');
            $table->enum('working_days_per_week', Employee::FULL_TIME_WORKING_DAYS_PER_WEEK);
            $table->integer('working_hours_per_day')->after('working_days_per_week')->default(8);
            $table->date('date_of_termination')->after('date_of_hire')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('employment_type');
            $table->dropColumn('working_days_per_week');
            $table->dropColumn('date_of_termination');
        });
    }
};
