<?php

namespace App\Http\Controllers;

use App\Enumerators\ErrorMessagesEnumerator;
use App\Http\Requests\PayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
    
    /*
     * Generate payroll of all the active employees of the company for the given period.
     */
    public function store(Request $request, Company $company): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period_id' => 'required|exists:periods,id'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        if (!$company->hasCoreSubscription) {
            return $this->sendError(ErrorMessagesEnumerator::COMPANY_NOT_SUBSCRIBED);
        }
        $period = $company->period($request->period_id);
        foreach ($company->employees->where('status', Employee::ACTIVE) as $employee) {
            try {
                DB::beginTransaction();
                $payroll = $this->payrollService->generate($period, $employee);
                DB::commit();
            } catch (Exception $e) {
                $errors[] = [
                    'employee_id' => $employee->id,
                    'employee_full_name' => $employee->fullName,
                    'error' => $e->getMessage()
                ];
                DB::rollBack();
            }
        }
        $this->forget($company);
        if (empty($errors)) {
            $payroll->makeHidden('employee');
            $payroll->makeHidden('period');
            return $this->sendResponse(new PayrollResource($payroll), 'Payroll created successfully.');
        } else {
            return $this->sendError(ErrorMessagesEnumerator::PAYROLL_GENERATION_FAILED, $errors);
        }
    }

    public function update(PayrollRequest $request, Company $company, int $payrollId): JsonResponse
    {
        $input = $request->validated();
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        if (!$payroll) {
            return $this->sendError("Payroll not found");
        }

        try {
            $payroll = $this->payrollService->update($payroll, $input);
            $payroll->makeHidden('employee');
            $payroll->makeHidden('period');
            $this->forget($company);
            return $this->sendResponse(new PayrollResource($payroll), 'Payroll updated successfully.');
        } catch (Exception $e) {
            return $this->sendError("Failed to update payroll.", $e->getMessage());
        }
    }
}
