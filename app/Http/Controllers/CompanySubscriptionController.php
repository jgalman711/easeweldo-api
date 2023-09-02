<?php

namespace App\Http\Controllers;

use App\Enumerators\SubscriptionEnumerator;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Employee;
use App\Models\Subscription;
use App\Models\SubscriptionPrices;
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

    public function store(SubscriptionRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employeeCount = $input['employee_count'] ?? $company->employees()->where('status', Employee::ACTIVE)->count();
        $now = Carbon::now();

        $subscription = Subscription::findOrFail($input['subscription_id']);

        $subscriptionPrice = SubscriptionPrices::where([
            'subscription_id' => $subscription->id,
            'months' => $input['months']
        ])->first();

        $companySubscription = CompanySubscription::updateOrCreate([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id
        ], [
            'status' => SubscriptionEnumerator::UNPAID_STATUS,
            'amount_per_employee' => $subscriptionPrice->price_per_employee,
            'amount' => $subscriptionPrice->price_per_employee * $employeeCount * $input['months'],
            'balance' => $subscriptionPrice->price_per_employee * $employeeCount * $input['months'],
            'start_date' => $now,
            'end_date' => $now->clone()->addMonth($input['months'])
        ]);

        if ($companySubscription->wasRecentlyCreated) {
            $message = "Company subscribed successfully to {$subscription->title}";
        } else {
            $message = "Company already subscribed to {$subscription->title}";
        }
        return $this->sendResponse(new BaseResource($companySubscription), $message);
    }

    public function update(SubscriptionRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $companySubscription = $company->companySubscriptions()->first();
        $input['end_date'] = Carbon::parse($companySubscription->end_date)->addMonths($input['months']);
        $companySubscription->update($input);
        return $this->sendResponse(
            new BaseResource($companySubscription),
            'Company subscription successfully updated.'
        );
    }

    public function delete(Company $company): JsonResponse
    {
        $companySubscription = $company->companySubscriptions()->first();
        $companySubscription->delete();
        return $this->sendResponse(
            new BaseResource($companySubscription),
            'Company subscription successfully deleted.'
        );
    }
}
