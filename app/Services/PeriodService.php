<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Period;
use Carbon\Carbon;
use Exception;

class PeriodService
{
    private const PAYROLL_ALLOWANCE_DAY = 5;
    private const SEMI_MONTHLY_DAYS = 15;
    private const MINIMUM_LAST_DAY = 28;

    public function initializeFromSalaryDate(array $data, Company $company): Period
    {
        $salaryDate = Carbon::parse($data['salary_date']);
        $data['end_date'] = $salaryDate->copy()->subDays(self::PAYROLL_ALLOWANCE_DAY);

        if ($salaryDate->day > self::MINIMUM_LAST_DAY || $data['end_date']->day > self::MINIMUM_LAST_DAY) {
            $data['end_date'] = Carbon::now()->setDay(25);
        }
        if ($data['type'] == Period::TYPE_MONTHLY) {
            $data['start_date'] = $data['end_date']->copy()->subMonth()->addDay();
        } elseif ($data['type'] == Period::TYPE_SEMI_MONTHLY) {
            $data['start_date'] = $data['end_date']->copy()->subDays(self::SEMI_MONTHLY_DAYS)->addDay();
        } elseif ($data['type'] == Period::TYPE_WEEKLY) {
            $data['end_date'] =  $salaryDate->copy()->subDays(7);
            $data['start_date'] =  $data['end_date']->copy()->subDays(6);
        }
        $companyPreviousPeriod = $company->periods()->latest()->first();
        throw_if(
            $companyPreviousPeriod && $data['start_date'] <= $companyPreviousPeriod->end_date,
            new Exception('This period overlaps the current period. Please adjust')
        );
        $data['company_id'] = $company->id;
        $data['company_period_number'] = $companyPreviousPeriod
            ? $companyPreviousPeriod->company_period_number + 1
            : 1;
        return Period::create($data);
    }
}

