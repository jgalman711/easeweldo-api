<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Period;
use App\Models\Setting;

class CompanyService
{
    public function initialize(Company $company): void
    {
        Setting::create([
            'company_id' => $company->id,
            'period_cycle' => Period::SUBTYPE_SEMI_MONTHLY,
            'salary_day' => [15, 30],
            'grace_period' => 15,
            'minimum_overtime' => 60
        ]);
    }
}
