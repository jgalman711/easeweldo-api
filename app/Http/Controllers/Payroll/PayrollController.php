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
use App\Services\Payroll\PayrollService;
use App\Traits\PayrollFilter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    use PayrollFilter;

    protected $payrollStrategy;

    public function __construct()
    {
        $this->payrollStrategy = PayrollStrategyFactory::createStrategy(PayrollEnumerator::TYPE_REGULAR);
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
        $employees = $this->payrollStrategy->getEmployees($company);
        list($payrolls, $errors) = $this->payrollStrategy->generate($employees, $period);
        $this->forget($company);
        return $this->sendResponse(new PayrollResource([$payrolls, $errors]), 'Payroll created successfully.');
    }

    public function update(PayrollRequest $request, Company $company, int $payrollId): JsonResponse
    {
        $input = $request->validated();
        $payroll = $company->payrolls->find('id', $payrollId);
        if (!$payroll) {
            return $this->sendError("Payroll not found");
        }

        try {
            $payroll = $this->payrollStrategy->update($payroll, $input);
            $payroll->makeHidden('employee');
            $payroll->makeHidden('period');
            $this->forget($company);
            return $this->sendResponse(new PayrollResource($payroll), 'Payroll updated successfully.');
        } catch (Exception $e) {
            return $this->sendError("Failed to update payroll.", $e->getMessage());
        }
    }

    /**
     * Regenerate Payroll
     */
    public function regenerate(Company $company, int $payrollId): JsonResponse
    {
        $payroll = $company->payrolls()->find($payrollId);
        if (!$payroll) {
            return $this->sendError("Unable to regenerate payroll. Payroll id not found.");
        }

        try {
            $payroll = $this->payrollStrategy->regenerate($payroll);
            return $this->sendResponse(new PayrollResource($payroll), 'Payroll regenerated successfully.');
        } catch (Exception $e) {
            return $this->sendError("Unable to regenerate payroll.", $e->getMessage());
        }
    }

}
