<?php

namespace App\Http\Controllers\Period;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Services\PeriodService;
use Exception;
use Illuminate\Http\JsonResponse;

class PeriodActionController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function update(Company $company, int $companyPeriodId, string $action): JsonResponse
    {
        try {
            $period = $company->periods()->where('company_period_id', $companyPeriodId)->first();
            if ($period) {
                $payrolls = $this->periodService->updateStatus($period, $action);
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
