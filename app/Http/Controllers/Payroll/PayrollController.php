<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\UpdatePayrollRequest;
use App\Http\Resources\Payroll\BasePayrollResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Services\Payroll\PayrollService;
use App\Traits\PayrollFilter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    use PayrollFilter;

    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
        $this->setCacheIdentifier('payrolls');
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $company->payrolls()->with('employee');
        $payrolls = $this->applyFilters($request, $payrolls, [
            'employee.first_name',
            'employee.last_name'
        ]);
        return $this->sendResponse(BasePayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }

    public function show(Company $company, Payroll $payroll): JsonResponse
    {
        $payrollWithEmployee = $this->remember($company, function () use ($payroll, $company) {
            if (!$company->payrolls->contains($payroll)) {
                return $this->sendError('Payroll not found.');
            }
            return $payroll->load('employee');
        }, $payroll);
        return $this->sendResponse(new BasePayrollResource($payrollWithEmployee), 'Payroll retrieved successfully.');
    }

    public function update(UpdatePayrollRequest $request, Company $company, Payroll $payroll)
    {
        try {
            $input = $request->validated();
            $payroll = $this->payrollService->update($payroll, $input);
            return $this->sendResponse(new BasePayrollResource($payroll), 'Payroll retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
