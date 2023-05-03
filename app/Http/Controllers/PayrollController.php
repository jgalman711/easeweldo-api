<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use Exception;
use Illuminate\Http\JsonResponse;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Employee $employee): JsonResponse
    {
        $payrolls = $employee->payrolls;
        return $this->sendResponse(BaseResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }
    
    public function store(PayrollRequest $request, Company $company): JsonResponse
    {
        $request->validated();
        $successPayroll = [];
        $failedPayroll = [];
        foreach ($company->employees as $employee) {
            try {
                $payroll = $this->payrollService->compute($employee, $request->period_id);
                array_push($successPayroll, $payroll);
            } catch (Exception $e) {
                array_push($failedPayroll, $e->getMessage());
            }
        }
        return $this->sendResponse(BaseResource::collection($failedPayroll), 'Payrolls retrieved successfully.');
    }

    public function show(Employee $employee, Payroll $payroll): JsonResponse
    {
        if ($employee->id != $payroll->employee_id) {
            return $this->sendError("Payroll does not belong to the user.");
        }
        return $this->sendResponse(new BaseResource($payroll), 'Payroll retrieved successfully');
    }

    // Payroll should not be updated.
    public function update(PayrollRequest $request, Employee $employee, Payroll $payroll): JsonResponse
    {
        if ($employee->id != $payroll->employee_id) {
            return $this->sendError("Payroll does not belong to the user.");
        }
        $input = $request->validated();
        $input['employee_id'] = $employee->id;
        $payroll->update($input);
        return $this->sendResponse(new BaseResource($payroll), 'Payroll updated successfully.');
    }

    public function destroy(Employee $employee, Payroll $payroll): JsonResponse
    {
        if ($employee->id != $payroll->employee_id) {
            return $this->sendError("Payroll does not belong to the user.");
        }
        return $this->sendResponse(new BaseResource($payroll), 'Payroll deleted successfully');
    }
}
