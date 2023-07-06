<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Employee;
use App\Services\TimeRecordService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class InitializeExpectedSchedule extends Command
{
    protected $signature = 'app:initialize-expected-schedule';

    protected $description = 'Set the expected schedule of all employees of active companies for the day.';

    public function handle()
    {
        $timeRecordService = app()->make(TimeRecordService::class);
        $employees = Employee::whereHas('company', function ($query) {
            $query->whereIn('status', [
                Company::STATUS_ACTIVE,
                Company::STATUS_TRIAL
            ]);
        })->get();

        foreach ($employees as $employee) {
            try {
                $timeRecordService->setExpectedScheduleOf($employee);
            } catch (Exception $e) {
                $this->error(
                    "Failed creating expected schedule for employee {$employee->fullName}: " . $e->getMessage()
                );
            }
        }
    }
}
