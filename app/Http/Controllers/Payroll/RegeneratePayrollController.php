<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\BasePayrollResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Services\Payroll\RegeneratePayrollService;
use Exception;
use Illuminate\Http\JsonResponse;

class RegeneratePayrollController extends Controller
{
    protected $regeneratePayrollService;

    public function __construct(RegeneratePayrollService $regeneratePayrollService)
    {
        $this->regeneratePayrollService = $regeneratePayrollService;
    }

    public function __invoke(Company $company, Payroll $payroll): JsonResponse
    {
        try {
            if ($payroll->status !== PayrollEnumerator::STATUS_PAID) {
                $payroll = $this->regeneratePayrollService->regenerate($payroll);
                return $this->sendResponse(new BasePayrollResource($payroll), 'Payroll regenerated successfully.');
            } else {
                return $this->sendError('Payroll regeneration failed. Payroll is already paid.');
            }
        } catch (Exception $e) {
            return $this->sendError('Payroll regeneration failed.', $e->getMessage());
        }
    }
}
