<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Services\Payroll\PayrollService;
use Exception;
use Illuminate\Http\JsonResponse;

class ActionPayrollController extends Controller
{
    protected $payrollService;

    private const ACTION_PAY = 'pay';
    private const ACTION_CANCEL = 'cancel';

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function update(Company $company, int $payrollId, string $action): JsonResponse
    {
        $payroll = $company->payrolls()->findOrFail($payrollId);
        if ($action == self::ACTION_PAY) {
            $status = PayrollEnumerator::STATUS_PAID;
        } elseif ($action == self::ACTION_CANCEL) {
            $status = PayrollEnumerator::STATUS_CANCELED;
        } else {
            throw new Exception('Invalid payroll action');
        }
        $payroll->status = $status;
        $payroll->save();
        return $this->sendResponse(new PayrollResource($payroll), 'Payrolls retrieved successfully.');
    }

    public function download(Company $company, int $payrollId)
    {
        $payroll = $company->payrolls()->find($payrollId);
        $pdf = $this->payrollService->download($payroll);
    }

    public function regenerate(Company $company, int $payrollId): JsonResponse
    {
        $payroll = $company->payrolls()->find($payrollId);
        try {
            $payroll = $this->payrollService->generate($payroll->period, $payroll->employee);
            return $this->sendResponse(new PayrollResource($payroll), 'Payroll regenerated successfully.');
        } catch (Exception $e) {
            return $this->sendError("Unable to regenerate payroll.", $e->getMessage());
        }
    }
}
