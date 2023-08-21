<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->setCacheIdentifier('subscriptions');
    }

    public function index(Company $company): JsonResponse
    {
        $this->forget($company);
        $subscriptions = $this->remember($company, function () use ($company) {
            if (!$company->isInSettlementPeriod()) {
                $employeeCount = $company->employees()->where('status', Employee::ACTIVE)->count() - 1;
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
            return $company->companySubscriptions()->with('subscription')->get();
        });
        return $this->sendResponse(new BaseResource($subscriptions), 'Company subscriptions retrieved successfully.');
    }
}
