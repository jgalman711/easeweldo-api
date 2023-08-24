<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
        $this->setCacheIdentifier('payrolls');
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $this->remember($company, function () use ($request, $company) {
            $payrollQuery = $company->payrolls();
            if ($request->has('employee_id')) {
                $payrollQuery->where('employee_id', $request->employee_id);
            }
            if ($request->has('period_id')) {
                $payrollQuery->where('period_id', $request->period_id);
            }
            return $this->applyFilters($request, $payrollQuery->with('employee'), [
                'status',
                'employee.first_name',
                'employee.last_name'
            ]);
        }, $request);
        return $this->sendResponse(PayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }

    public function show(Company $company, Payroll $payroll): JsonResponse
    {
        $payrollWithEmployee = $this->remember($company, function () use ($payroll, $company) {
            if (!$company->payrolls->contains($payroll)) {
                return $this->sendError('Payroll not found.');
            }
            return $payroll->load('employee');
        });
        return $this->sendResponse(new PayrollResource($payrollWithEmployee), 'Payroll retrieved successfully.');
    }
    
    public function store(PayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employee = $company->getEmployeeById($request->employee_id);
        if ($employee->status != Employee::ACTIVE) {
            return $this->sendError("Unable to generate payroll for {$employee->status} employee.");
        }
        $period = $company->period($request->period_id);
        $payroll = $this->payrollService->generate($period, $employee, $input);
        $this->forget($company);
        return $this->sendResponse(new PayrollResource($payroll), 'Payroll created successfully.');
    }
}
