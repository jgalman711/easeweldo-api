<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\BasePayrollResource;
use App\Models\Company;
use App\Models\Payroll;

class CancelPayrollController extends Controller
{
    public function __invoke(Company $company, Payroll $payroll)
    {
        try {
            $payroll->state()->cancel();
            return $this->sendResponse(new BasePayrollResource($payroll), 'Payroll cancelled successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
