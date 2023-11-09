<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinalPayrollRequest;
use App\Http\Requests\PayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Traits\PayrollFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinalPayrollController extends Controller
{
    use PayrollFilter;

    protected $finalPayrollStrategy;

    public function __construct()
    {
        $this->finalPayrollStrategy = PayrollStrategyFactory::createStrategy(PayrollEnumerator::TYPE_FINAL);
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $company->payrolls()->where('type', PayrollEnumerator::TYPE_FINAL)->with('employee');
        $finalPayroll = $this->applyFilters($request, $payrolls);
        return $this->sendResponse(PayrollResource::collection($finalPayroll),
            "Payrolls retrieved successfully.");
    }

    public function show(Company $company, int $payrollId)
    {
        $payroll = Payroll::findOrFail($payrollId);
        if (!$company->payrolls->contains($payroll)) {
            return $this->sendError('Payroll not found.');
        }
        $payroll->load('employee');
        return $this->sendResponse(new PayrollResource($payroll), 'Payroll retrieved successfully.');
    }

    public function store(FinalPayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employees = $this->finalPayrollStrategy->getEmployees($company, $input);
        $input['company'] = $company;
        list($payrolls, $errors) = $this->finalPayrollStrategy->generate($employees, $input);

        if (empty($payrolls) && !empty($errors)) {
            return $this->sendError($errors, "Final payroll failed.");
        } elseif (empty(!$errors) && !empty($payrolls)) {
            $message = "Final payroll generated partially successfully.";
        } else {
            $message = "Final payroll generated successfully.";
        }
        return $this->sendResponse(PayrollResource::collection([
            'success' => $payrolls,
            'failed' => $errors
        ]), $message);
    }
}
