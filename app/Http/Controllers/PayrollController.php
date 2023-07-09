<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Services\PayrollService;
use App\Traits\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    use Filter;

    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrollQuery = $company->payrolls();
        if ($request->has('employee_id')) {
            $payrollQuery->where('employee_id', $request->employee_id);
        }
        if ($request->has('period_id')) {
            $payrollQuery->where('period_id', $request->period_id);
        }
        $payrolls = $this->applyFilters($request, $payrollQuery->with('employee'), [
            'status',
            'employee.first_name',
            'employee.last_name'
        ]);
        return $this->sendResponse($payrolls, 'Payrolls retrieved successfully.');
    }

    public function show(Company $company, Payroll $payroll): JsonResponse
    {
        if (!$company->payrolls->contains($payroll)) {
            return $this->sendError('Payroll not found.');
        }
        return $this->sendResponse(new BaseResource($payroll), 'Payroll retrieved successfully.');
    }
    
    public function store(PayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employee = $company->getEmployeeById($request->employee_id);
        $period = $company->period($request->period_id);
        $payroll = $this->payrollService->generate($period, $employee, $input);
        return $this->sendResponse(new BaseResource($payroll), 'Payroll created successfully.');
    }

    public function update(PayrollRequest $request, Company $company, int $employeeId, int $payrollId): JsonResponse
    {
        $input = $request->validated();
        $employee = $company->getEmployeeById($employeeId);
        $input['company_id'] = $company->id;
        $input['employee_id'] = $employee->id;
        $payroll = $employee->payrolls()->where('id', $payrollId)->first();
        $payroll->update($input);
        return $this->sendResponse(new BaseResource($payroll), 'Payroll updated successfully.');
    }

    public function destroy(Company $company, Payroll $payroll): JsonResponse
    {
        if (!$company->payrolls->contains($payroll)) {
            return $this->sendError('Payroll not found.');
        }
        $payroll->delete();
        return $this->sendResponse(new BaseResource($payroll), 'Payroll deleted successfully.');
    }
}
