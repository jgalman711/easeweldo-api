<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Services\PeriodService;
use Exception;
use Illuminate\Http\JsonResponse;

class PayEmployeeController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function update(Company $company, int $companyPeriodId): JsonResponse
    {
        try {
            $period = $company->periods()->where('company_period_id', $companyPeriodId)->first();
            if ($period) {
                $payrolls = $this->periodService->pay($period);
                return $this->sendResponse(PayrollResource::collection($payrolls),
                    'Payrolls for the selected period is now paid.'
                );
            }
            return $this->sendError("Period not found.");
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
