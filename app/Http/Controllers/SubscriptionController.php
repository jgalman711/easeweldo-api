<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subscriptions = Cache::remember('subscriptions', 3600, function () {
            return Subscription::with('subscriptionPrices')->get();
        });

        return $this->sendResponse(
            BaseResource::collection($subscriptions),
            'Subscriptions retrieved successfully.'
        );
    }

    public function show(Subscription $subscription): JsonResponse
    {
        $subscription = Cache::remember('subscription', 3600, function () use ($subscription) {
            $subscription->load('subscriptionPrices');
            return $subscription;
        });
        return $this->sendResponse(new BaseResource($subscription), 'Subscription retrieved successfully.');
    }
}
