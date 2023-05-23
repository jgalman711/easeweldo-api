<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PeriodService;
use App\Services\TimeRecordService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/*
 * This will be run every 00:05 of the day to generate the next period
 */

class InitializeNextPeriod extends Command
{
    protected $signature = 'app:initialize-next-period';

    protected $description = 'Initialize the next period based from previous period.';

    public function handle()
    {
        $companies = Company::where('status', Company::STATUS_ACTIVE)->get();
        $periodService = app()->make(PeriodService::class);
        $timeRecordService = app()->make(TimeRecordService::class);
        foreach ($companies as $company) {
            try {
                DB::beginTransaction();
                $period = $periodService->initializeFromPreviousPeriod($company);
                if ($period) {
                    foreach ($company->employees as $employee) {
                        for ($date = $period->start_date; $date <= $period->end_date; $date->addDay()) {
                            $timeRecordService->create($employee, $date, $date);
                        }
                    }
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e->getMessage();
            }
        }
    }
}
