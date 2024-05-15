<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payroll;

class DownloadPayrollController extends Controller
{
    public function __invoke(Company $company, Payroll $payroll)
    {
        try {
            return $payroll->download();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
