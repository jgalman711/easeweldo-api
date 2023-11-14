<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;

class ActionPayrollController extends Controller
{
    private const ACTION_PAY = 'pay';
    private const ACTION_CANCEL = 'cancel';

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
}
