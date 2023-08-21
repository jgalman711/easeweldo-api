<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;

class SubscriptionService
{
    public function calculate(Company $company)
    {
        foreach ($company->companySubscriptions as $companySubscription) {
            $amount = $companySubscription->subscription->amount;
            $numberOfEmployees = $company->employees->where('status', Employee::ACTIVE)->count();
            $companySubscription->amount = $amount * $numberOfEmployees;
            $companySubscription->save();
        }
    }
}
