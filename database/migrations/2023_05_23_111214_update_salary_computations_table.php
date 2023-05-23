<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->renameColumn('total_sick_leaves', 'total_sick_leave_hours');
            $table->renameColumn('total_vacation_leaves', 'total_vacation_leave_hours');
            $table->renameColumn('available_sick_leaves', 'available_sick_leave_hours');
            $table->renameColumn('available_vacation_leaves', 'available_vacation_leave_hours');
        });
    }

    public function down(): void
    {
        Schema::table('salary_computations', function (Blueprint $table) {
            $table->renameColumn('total_sick_leave_hours', 'total_sick_leaves');
            $table->renameColumn('total_vacation_leave_hours', 'total_vacation_leaves');
            $table->renameColumn('available_sick_leave_hours', 'available_sick_leaves');
            $table->renameColumn('available_vacation_leave_hours', 'available_vacation_leaves');
        });
    }
};
