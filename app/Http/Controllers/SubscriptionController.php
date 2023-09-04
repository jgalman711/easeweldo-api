<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::with('subscriptionPrices')->get();
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
