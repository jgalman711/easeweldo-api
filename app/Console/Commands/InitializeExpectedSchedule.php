<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\TimeRecordService;
use Exception;
use Illuminate\Console\Command;

class InitializeExpectedSchedule extends Command
{
    protected $signature = 'app:initialize-expected-schedule';

    protected $description = 'Command description';

    public function handle()
    {
        $timeRecordService = app()->make(TimeRecordService::class);
        $companies = Company::where('status', Company::STATUS_ACTIVE)->get();
        foreach ($companies as $company) {
            foreach ($company->employees as $employee) {
                try {
                    $timeRecordService->create($employee);
                } catch (Exception $e) {
                    echo "Employee {$employee->id}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
}
