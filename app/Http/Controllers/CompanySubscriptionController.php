<?php

namespace App\Http\Controllers;

use App\Enumerators\SubscriptionEnumerator;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\CompanySubscriptionResource;
use App\Mail\UserSubscribed;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Employee;
use App\Models\Subscription;
use App\Models\SubscriptionPrices;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CompanySubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->setCacheIdentifier('subscriptions');
    }

    public function index(Company $company): JsonResponse
    {
        $subscriptions = $this->remember($company, function () use ($company) {
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
            return $company->companySubscriptions()->with('subscription')->get();
        });
        return $this->sendResponse(CompanySubscriptionResource::collection($subscriptions),
            'Company subscriptions retrieved successfully.');
    }

    public function store(SubscriptionRequest $request, Company $company)
    {
        $input = $request->validated();
        $employeeCount = $input['employee_count'] ?? $company->employees()->where('status', Employee::ACTIVE)->count();
        $now = Carbon::now();

        $subscription = Subscription::findOrFail($input['subscription_id']);

        $subscriptionPrice = SubscriptionPrices::where([
            'subscription_id' => $subscription->id,
            'months' => $input['months']
        ])->first();

        $companySubscription = CompanySubscription::firstOrCreate([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id
        ], [
            'status' => SubscriptionEnumerator::UNPAID_STATUS,
            'amount_per_employee' => $subscriptionPrice->price_per_employee,
            'employee_count' => $employeeCount,
            'months' => $input['months'],
            'amount' => $subscriptionPrice->price_per_employee * $employeeCount * $input['months'],
            'balance' => $subscriptionPrice->price_per_employee * $employeeCount * $input['months'],
            'start_date' => $now,
            'end_date' => $now->clone()->addMonth($input['months'])
        ]);

        if ($companySubscription->wasRecentlyCreated) {
            $message = "Company subscribed successfully to {$subscription->title}";
            $user = Auth::user();
            $companySubscription->load('company', 'subscription');
            Mail::to($user->email_address)->send(new UserSubscribed($companySubscription));
        } else {
            $message = "Company is already subscribed to {$subscription->title}";
        }
        return $this->sendResponse(new CompanySubscriptionResource($companySubscription), $message);
    }

    public function update(SubscriptionRequest $request, Company $company): JsonResponse
    {
        try {
            $input = $request->validated();
            $companySubscription = $company->companySubscriptions()->first();

            if (isset($input['action']) && $input['action'] == SubscriptionEnumerator::UPGRADE) {
                $this->subscriptionService->upgrade($companySubscription, $input);
            } elseif (isset($input['action']) && $input['action'] == SubscriptionEnumerator::RENEW) {
                $this->subscriptionService->renew($companySubscription, $input);
            }

            $input['end_date'] = Carbon::parse($companySubscription->end_date)->addMonths($input['months']);
            $companySubscription->update($input);
            return $this->sendResponse(
                new CompanySubscriptionResource($companySubscription),
                'Company subscription successfully updated.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function delete(Company $company): JsonResponse
    {
        $companySubscription = $company->companySubscriptions()->first();
        $companySubscription->delete();
        return $this->sendResponse(
            new CompanySubscriptionResource($companySubscription),
            'Company subscription successfully deleted.'
        );
    }
}
