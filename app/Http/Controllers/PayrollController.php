<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Period;
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
        $input = $request->validated();
        $successPayroll = [];
        $failedPayroll = [];
        $period = $company->getPeriodById($input['period_id']);
        $period->status = Period::STATUS_PROCESSING;
        $period->save();

        foreach ($company->employees as $employee) {
            try {
                $payroll = $this->payrollService->generate($period, $employee);
                array_push($successPayroll, $payroll);
            } catch (Exception $e) {
                array_push($failedPayroll, $e->getMessage());
            }
        }

        if (empty($failedPayroll)) {
            $period->status = Period::STATUS_COMPLETED;
        } elseif (empty($successPayroll)) {
            $period->status = Period::STATUS_FAILED;
        } else {
            $period->status = Period::STATUS_ATTENTION_REQUIRED;
        }
        $period->save();

        return $this->sendResponse([
            'success' => $successPayroll,
            'failed' => $failedPayroll
        ], 'Payrolls created successfully.');
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