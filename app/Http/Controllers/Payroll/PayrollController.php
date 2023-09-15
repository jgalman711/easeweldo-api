<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\ErrorMessagesEnumerator;
use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Traits\PayrollFilter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    use PayrollFilter;

    protected $specialPayrollStrategy;

    public function __construct()
    {
        $this->specialPayrollStrategy = PayrollStrategyFactory::createStrategy(PayrollEnumerator::TYPE_REGULAR);
        $this->setCacheIdentifier('payrolls');
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $this->applyFilters($request, $company->payrolls()->with('employee'), [
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
    
    /*
     * Generate payroll of all the active employees of the company for the given period.
     */
    public function store(Request $request, Company $company): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period_id' => 'required|exists:periods,company_period_id'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        if (!$company->hasCoreSubscription) {
            return $this->sendError(ErrorMessagesEnumerator::COMPANY_NOT_SUBSCRIBED);
        }
        $period = $company->period($request->period_id);
        $employees = $company->employees()->where('status', Employee::ACTIVE)->get();
        list($payrolls, $errors) = $this->specialPayrollStrategy->generate($employees, $period);
        $this->forget($company);
        return $this->sendResponse(new PayrollResource([$payrolls, $errors]), 'Payroll created successfully.');
    }

    public function update(PayrollRequest $request, Company $company, int $payrollId): JsonResponse
    {
        $input = $request->validated();
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        if (!$payroll) {
            return $this->sendError("Payroll not found");
        }

        try {
            $payroll = $this->specialPayrollStrategy->regenerate($payroll, $input);
            $payroll->makeHidden('employee');
            $payroll->makeHidden('period');
            $this->forget($company);
            return $this->sendResponse(new PayrollResource($payroll), 'Payroll updated successfully.');
        } catch (Exception $e) {
            return $this->sendError("Failed to update payroll.", $e->getMessage());
        }
    }
}
