<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;

class PaymentMethodController extends Controller
{
    public function index(): JsonResponse
    {
        $paymentMethods = PaymentMethod::where('status', PaymentMethod::STATUS_ACTIVE)->get();
        return $this->sendResponse(BaseResource::collection($paymentMethods), 'Payment methods succesfully retrieved.');
    }
}
