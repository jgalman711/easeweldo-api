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

    public function updateSubscriptionDetails(Company $company): void
    {
        if (!$company->isInSettlementPeriod()) {
            $employeeCount = $company->employees()->where('status', Employee::ACTIVE)->count();
            foreach ($company->companySubscriptions as $companySubscription) {
                $companySubscription->amount = $employeeCount * $companySubscription->amount_per_employee;
                $balance = $companySubscription->amount - $companySubscription->amount_paid;
                if ($balance < 0) {
                    $companySubscription->balance = 0;
                    $companySubscription->overpaid_balance = abs($balance);
                } else {
                    $companySubscription->balance = $balance;
                }
                $companySubscription->save();
            }
        }
    }
}
