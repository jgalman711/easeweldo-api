<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\NthMonthPayRequest;
use App\Http\Requests\Payroll\UpdatePayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Traits\PayrollFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NthMonthPayrollController extends Controller
{
    use PayrollFilter;

    protected $nthMonthPayrollStrategy;

    public function __construct()
    {
        $this->nthMonthPayrollStrategy = PayrollStrategyFactory::createStrategy(PayrollEnumerator::TYPE_NTH_MONTH_PAY);
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $company->payrolls()->where('type', PayrollEnumerator::TYPE_NTH_MONTH_PAY)->with('employee');
        $nthMonthPayroll = $this->applyFilters($request, $payrolls);
        return $this->sendResponse(PayrollResource::collection($nthMonthPayroll),
            "Payrolls retrieved successfully.");
    }

    public function store(NthMonthPayRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $payrolls = $this->nthMonthPayrollStrategy->generate($company, $input);
        return $this->sendResponse(PayrollResource::collection($payrolls),
            "{$input['description']} generated successfully.");
    }

    public function show(Company $company, int $payrollId): JsonResponse
    {
        $payroll = Payroll::findOrFail($payrollId);
        if (!$company->payrolls->contains($payroll)) {
            return $this->sendError('Payroll not found.');
        }
        $payroll->load('employee');
        return $this->sendResponse(new PayrollResource($payroll), 'Payroll retrieved successfully.');
    }

    public function update(UpdatePayrollRequest $request, Company $company, int $payrollId): JsonResponse
    {
        $input = $request->validated();
        $payroll = $this->nthMonthPayrollStrategy->update($company, $payrollId, $input);
        return $this->sendResponse(new PayrollResource($payroll), 'Payroll retrieved successfully.');
    }
}
