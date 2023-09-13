<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->setCacheIdentifier('subscriptions');
    }

    public function index(Request $request): JsonResponse
    {
        $subscriptions = $this->remember(self::ADMIN_CACHE_KEY, function () use ($request) {
            return $this->subscriptionService->getSubscriptions($request);
        }, $request);

        return $this->sendResponse(
            BaseResource::collection($subscriptions),
            'Subscriptions retrieved successfully.'
        );
    }

    public function show(Subscription $subscription): JsonResponse
    {
        $subscription = $subscription->load('subscriptionPrices');
        return $this->sendResponse(new BaseResource($subscription), 'Subscription retrieved successfully.');
    }
}
