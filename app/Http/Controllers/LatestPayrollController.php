<?php

namespace App\Http\Controllers;

use App\Enumerators\PayrollEnumerator;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class LatestPayrollController extends Controller
{
    public function show(Company $company): JsonResponse
    {
        $latestPeriod = $company->periods()->latest()->first();
        $payrolls = $company->payrolls()
            ->where('type', PayrollEnumerator::TYPE_REGULAR)
            ->where('period_id', $latestPeriod->id)
            ->with('employee')
            ->get();
        return $this->sendResponse(PayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }
}
