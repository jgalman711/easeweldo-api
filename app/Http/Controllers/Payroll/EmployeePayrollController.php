<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\BasePayrollResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Traits\PayrollFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeePayrollController extends Controller
{
    use PayrollFilter;
    
    public function index(Request $request, Company $company, Employee $employee): JsonResponse
    {
        $payrolls = $this->applyFilters($request, $employee->payrolls()->with(['employee.user', 'period']));
        if ($payrolls) {
            return $this->sendResponse(BasePayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
        } else {
            return $this->sendError('Payrolls not found');
        }
    }

    public function show(Company $company, Employee $employee, Payroll $payroll): JsonResponse
    {
        return $this->sendResponse(new BasePayrollResource($payroll), 'Payroll retrieved successfully.');
    }
}
