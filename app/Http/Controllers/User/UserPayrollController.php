<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class UserPayrollController extends Controller
{
    public function index(Company $company, int $employeeId): JsonResponse
    {
        $payrolls = $company->employees()->find($employeeId)->payrolls()->with('period')->get();
        if ($payrolls) {
            return $this->sendResponse(PayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
        } else {
            return $this->sendError("Payrolls not found");
        }
    }

    public function show(Company $company, int $employeeId, int $payrollId): JsonResponse
    {
        $payroll = $company->employees()->find($employeeId)->payrolls()->find($payrollId);
        if ($payroll) {
            return $this->sendResponse(new PayrollResource($payroll), 'Payroll retrieved successfully.');
        } else {
            return $this->sendError("Payroll not found");
        }
    }
}
