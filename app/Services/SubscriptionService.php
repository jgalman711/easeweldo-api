<?php

namespace App\Services;

use App\Enumerators\SubscriptionEnumerator;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Employee;
use App\Models\Subscription;
use App\Models\SubscriptionPrices;
use Carbon\Carbon;
use Exception;

class SubscriptionService
{
    public const REGULAR_ONE_MONTH = 1;

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


    public function upgrade(CompanySubscription $companySubscription, array $upgradeData): CompanySubscription
    {
        throw_if(
            $upgradeData['subscription_id'] <= $companySubscription->subscription_id,
            new Exception("Invalid upgrade plan.")
        );
        $employeeCount = $this->getEmployeeCount($companySubscription->employee_count, $upgradeData);
        $upgradedSubscriptionPlan = Subscription::with(['subscriptionprices' => function ($query) {
            $query->where('months', self::REGULAR_ONE_MONTH);
        }])->findOrFail($upgradeData['subscription_id']);

        $remainingMonths = Carbon::now()->diffInMonths($companySubscription->end_date);
        $pricePerEmployee = floatval($upgradedSubscriptionPlan->subscriptionprices->first()->price_per_employee);
        $amountToPay = $pricePerEmployee * $employeeCount * $remainingMonths;
        $companySubscription->update([
            'subscription_id' => $upgradedSubscriptionPlan->id,
            'status' => SubscriptionEnumerator::UNPAID_STATUS,
            'amount_per_employee' => $pricePerEmployee,
            'employee_count' => $employeeCount,
            'amount' => $companySubscription->amount + $amountToPay,
            'balance' => $amountToPay - $companySubscription->balance
        ]);
        return $companySubscription;
    }

    private function getEmployeeCount(int $employeeCount, array $upgradeData): int
    {
        if (isset($upgradeData['employee_count']) && $upgradeData['employee_count'] > $employeeCount) {
            return $upgradeData['employee_count'];
        } else {
            return $employeeCount;
        }
    }
}
