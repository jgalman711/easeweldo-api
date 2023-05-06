<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
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

    public function index(Company $company): JsonResponse
    {
        $payrolls = $company->payrolls;
        return $this->sendResponse(BaseResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }
    
    public function store(PayrollRequest $request, Company $company): JsonResponse
    {
        $request->validated();
        $successPayroll = [];
        $failedPayroll = [];
        foreach ($company->employees as $employee) {
            try {
                $period = $company->getPeriodById($request->period_id);
                $payroll = $this->payrollService->compute($employee, $period);
                array_push($successPayroll, $payroll);
            } catch (Exception $e) {
                array_push($failedPayroll, $e->getMessage());
            }
        }
        return $this->sendResponse([
            $successPayroll,
            $failedPayroll
        ], 'Payrolls retrieved successfully.');
    }

    public function show(Company $company, int $payrollId): JsonResponse
    {
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        return $this->sendResponse(new BaseResource($payroll), 'Payroll retrieved successfully');
    }

    // Payroll should not be updated I think. But will do this feature next.
    public function update(PayrollRequest $request, Company $company, int $payrollId): JsonResponse
    {
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        $input = $request->validated();
        $payroll->update($input);
        return $this->sendResponse(new BaseResource($payroll), 'Payroll updated successfully.');
    }

    public function destroy(Company $company, int $payrollId): JsonResponse
    {
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        return $this->sendResponse(new BaseResource($payroll), 'Payroll deleted successfully');
    }
}
