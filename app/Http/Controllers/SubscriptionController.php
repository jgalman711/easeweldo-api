<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Cache::remember('subscriptions', 3600, function () {
            return Subscription::all();
        });

        return $this->sendResponse(
            BaseResource::collection($subscriptions),
            'Subscriptions retrieved successfully.'
        );
    }
}
