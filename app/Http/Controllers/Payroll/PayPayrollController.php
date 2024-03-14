<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\BasePayrollResource;
use App\Models\Company;
use App\Models\Payroll;

class PayPayrollController extends Controller
{
    public function __invoke(Company $company, Payroll $payroll)
    {
        try {
            $payroll->state()->pay();
            return $this->sendResponse(new BasePayrollResource($payroll), 'Payroll paid successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
