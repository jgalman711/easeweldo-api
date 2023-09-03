<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\SubscriptionPrices;
use Illuminate\Http\Request;

class SubscriptionPricesController extends Controller
{
    public function index(Request $request)
    {
        $prices = $this->applyFilters($request, SubscriptionPrices::with('subscription'), [
            'months'
        ]);
        return $this->sendResponse(BaseResource::collection($prices), 'Subscription prices retrieved successfully.');
    }
}
