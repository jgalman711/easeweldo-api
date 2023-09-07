<?php

namespace App\Services;

use App\Enumerators\ErrorMessagesEnumerator;
use App\Enumerators\SubscriptionEnumerator;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Employee;
use App\Models\Subscription;
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
            $upgradeData['subscription_id'] < $companySubscription->subscription_id,
            new Exception("Invalid upgrade plan.")
        );
        $employeeCount = $this->getEmployeeCount($companySubscription->employee_count, $upgradeData);
        $upgradedSubscriptionPlan = Subscription::with(['subscriptionPrices' => function ($query) {
            $query->where('months', self::REGULAR_ONE_MONTH);
        }])->findOrFail($upgradeData['subscription_id']);

        $remainingMonths = Carbon::now()->diffInMonths($companySubscription->end_date);
        $subscriptionPrices = $upgradedSubscriptionPlan->subscriptionPrices->first();
        $pricePerEmployee = floatval($subscriptionPrices->price_per_employee);
        $amountToPay = $pricePerEmployee * $employeeCount * $remainingMonths;
        $companySubscription->update([
            'subscription_id' => $upgradedSubscriptionPlan->id,
            'status' => SubscriptionEnumerator::UNPAID_STATUS,
            'amount_per_employee' => $pricePerEmployee,
            'employee_count' => $employeeCount,
            'amount' => $amountToPay,
            'balance' => $amountToPay - $companySubscription->amount_paid
        ]);
        $companySubscription->load('company', 'subscription');
        return $companySubscription;
    }

    /*
     * Return mixed. Collection or CompanySubscription Model.
     */
    public function renew(CompanySubscription $companySubscription, array $renewData)
    {
        if ($companySubscription->renewals->isNotEmpty()) {
            return [
                'companySubscription' => $companySubscription->renewals,
                'message' => "Unable to renew your subscription."
            ];
        }
        return $this->subscribe($companySubscription->company, $renewData, $companySubscription);
    }

    public function subscribe(
        Company $company,
        array $subscriptionData,
        CompanySubscription $companySubscription = null
    ): CompanySubscription {
        $activeEmployees = $company->employees()->where('status', Employee::ACTIVE)->count();
        $employeeCount = $this->getEmployeeCount($activeEmployees, $subscriptionData);

        $startDate = $companySubscription
            ? Carbon::parse($companySubscription->end_date)->addDay()
            : Carbon::now();

        $subscriptionPlan = Subscription::with(['subscriptionPrices' => function ($query) use ($subscriptionData) {
            $query->where('months', $subscriptionData['months']);
        }])->findOrFail($subscriptionData['subscription_id']);

        $subscriptionPrice = $subscriptionPlan->subscriptionPrices->first();
        $pricePerEmployee = $subscriptionPrice->price_per_employee;

        return CompanySubscription::create([
            'company_id' => $company->id,
            'subscription_id' => $subscriptionPlan->id,
            'renewed_from_id' => optional($companySubscription)->id,
            'status' => SubscriptionEnumerator::UNPAID_STATUS,
            'amount_per_employee' => $pricePerEmployee,
            'employee_count' => $employeeCount,
            'months' => $subscriptionData['months'],
            'amount' => $pricePerEmployee * $employeeCount * $subscriptionData['months'],
            'balance' => $pricePerEmployee * $employeeCount * $subscriptionData['months'],
            'start_date' => $startDate,
            'end_date' => $startDate->clone()->addMonth($subscriptionData['months'])
        ])->load('company', 'subscription');
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
