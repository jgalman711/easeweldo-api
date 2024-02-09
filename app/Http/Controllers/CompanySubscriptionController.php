<?php

namespace App\Http\Controllers;

use App\Enumerators\SubscriptionEnumerator;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\CompanySubscriptionResource;
use App\Models\Company;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanySubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->setCacheIdentifier('subscriptions');
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $subscriptions = $this->remember($company, function () use ($company, $request) {
            return $this->applyFilters($request, $company->companySubscriptions()->with('subscription'));
        }, $request);
        return $this->sendResponse(CompanySubscriptionResource::collection($subscriptions),
            'Company subscriptions retrieved successfully.');
    }

    public function show(Company $company, int $companySubscriptionId): JsonResponse
    {
        $companySubscription = $this->remember($company, function () use ($company, $companySubscriptionId) {
            return $company->companySubscriptions()->where('id', $companySubscriptionId)->firstOrFail();
        }, $companySubscriptionId);
        return $this->sendResponse(
            new CompanySubscriptionResource($companySubscription),
            'Company subscription retrieved successfully.'
        );
    }

    public function store(SubscriptionRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $currentSubscription = $company->companySubscriptions()->latest()->first();
        $companySubscription = $this->subscriptionService->subscribe($company, $input, $currentSubscription);
        $this->forget($company);
        return $this->sendResponse(
            new CompanySubscriptionResource($companySubscription),
            "Company subscribed successfully to {$companySubscription->subscription->title}"
        );
    }

    public function update(SubscriptionRequest $request, Company $company, int $companySubscriptionId): JsonResponse
    {
        try {
            $input = $request->validated();
            $companySubscription = $company->companySubscriptions()->where('id', $companySubscriptionId)->firstOrFail();
            if ($input['action'] == SubscriptionEnumerator::UPGRADE) {
                $companySubscription = $this->subscriptionService->upgrade($companySubscription, $input);
            } elseif ($input['action'] == SubscriptionEnumerator::RENEW) {
                $companySubscription = $this->subscriptionService->renew($companySubscription, $input);
                if (is_array($companySubscription)) {
                    return $this->sendError(
                        $companySubscription['message'],
                        $companySubscription['companySubscription']
                    );
                }
            }
            $this->forget($company);
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
