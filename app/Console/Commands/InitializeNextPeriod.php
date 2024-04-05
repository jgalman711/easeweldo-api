<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PeriodService;
use Exception;
use Illuminate\Console\Command;

/*
 * This will be run every 00:05 of the day to generate the next period
 */

class InitializeNextPeriod extends Command
{
    protected $signature = 'app:initialize-next-period {company-slug?}';

    protected $description = 'Initialize the next period based from previous period.';

    public function handle()
    {
        $companySlug = $this->argument('company-slug');
        $companiesQuery = Company::query();
        if ($companySlug) {
            $companiesQuery->where('slug', $companySlug);
        } else {
            $companiesQuery->where('status', Company::STATUS_ACTIVE);
        }
        $companies = $companiesQuery->get();
        $periodService = app()->make(PeriodService::class);
        foreach ($companies as $company) {
            try {
                $periodService->initializeFromPreviousPeriod($company);
                $this->info("Next period initialized successfully for company {$company->name}.");
            } catch (Exception $e) {
                $this->error("Unable to initialize next period for company {$company->name}. {$e->getMessage()}");
            }
        }
    }
}
