<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Employee;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CompanySubscriptionController extends Controller
{
    public function __construct()
    {
        $this->setCacheIdentifier('subscriptions');
    }

    public function index(Company $company): JsonResponse
    {
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

    public function store(SubscriptionRequest $request, Company $company)
    {
        $input = $request->validated();
        $employeeCount = $company->employees()->where('status', Employee::ACTIVE)->count();
        $now = Carbon::now();

        foreach ($input['subscriptions'] as $subscriptionId) {
            $subscription = Subscription::find($subscriptionId);
            CompanySubscription::updateOrCreate([
                'company_id' => $company->id,
                'subscription_id' => $subscription->id
            ], [
                'status' => Subscription::UNPAID_STATUS,
                'amount_per_employee' => $subscription->amount,
                'amount' => $subscription->amount * $employeeCount,
                'start_date' => $now,
                'end_date' => $now->clone()->addMonth($input['months'])
            ]);
        }
        return $this->sendResponse(
            BaseResource::collection($company->companySubscriptions),
            'Company subscribed successfully.'
        );
    }
}
