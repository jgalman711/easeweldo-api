<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\UpdateRegularPayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Services\Payroll\PayrollService;
use App\Traits\PayrollFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    use PayrollFilter;

    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->setCacheIdentifier('payrolls');
        $this->payrollService = $payrollService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $company->payrolls()->with('employee');
        $payrolls = $this->applyFilters($request, $payrolls, [
            'employee.first_name',
            'employee.last_name'
        ]);
        return $this->sendResponse(PayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }

    public function show(Company $company, Payroll $payroll): JsonResponse
    {
        $payrollWithEmployee = $this->remember($company, function () use ($payroll, $company) {
            if (!$company->payrolls->contains($payroll)) {
                return $this->sendError('Payroll not found.');
            }
            return $payroll->load('employee');
        }, $payroll);
        return $this->sendResponse(new PayrollResource($payrollWithEmployee), 'Payroll retrieved successfully.');
    }

    public function update(UpdateRegularPayrollRequest $request, Company $company, Payroll $payroll): JsonResponse
    {
        $payroll = $this->payrollService->update($payroll, $request);
        return $this->sendResponse(new PayrollResource($payroll), 'Payroll updated successfully');
    }
}
