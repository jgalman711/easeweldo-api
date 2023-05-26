<?php

use App\Models\Employee;
use App\Models\TimeRecord;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('time_records', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        TimeRecord::chunk(200, function ($records) {
            foreach ($records as $record) {
                $employee = Employee::find($record->employee_id);
                $record->update(['company_id' => $employee->company_id]);
            }
        });

        Schema::table('time_records', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('time_records', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
