<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PeriodService;
use Illuminate\Console\Command;

class InitializeNextPeriod extends Command
{
    protected $signature = 'app:initialize-next-period';

    protected $description = 'Initialize the next period based from previous period.';

    public function handle()
    {
        $companies = Company::where('status', Company::STATUS_ACTIVE)->get();
        $periodService = app()->make(PeriodService::class);
        foreach ($companies as $company) {
            $periodService->initializeFromPreviousPeriod($company);
        }
    }
}
