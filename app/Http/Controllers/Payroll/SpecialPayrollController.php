<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\SpecialPayrollRequest;
use App\Http\Requests\Payroll\UpdatePayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpecialPayrollController extends Controller
{
    protected $specialPayrollStrategy;

    public function __construct()
    {
        $this->specialPayrollStrategy = PayrollStrategyFactory::createStrategy(PayrollEnumerator::TYPE_SPECIAL);
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $company->payrolls()->where('type', PayrollEnumerator::TYPE_SPECIAL)->with('employee');
        $specialPayroll = $this->applyFilters($request, $payrolls);
        return $this->sendResponse(PayrollResource::collection($specialPayroll),
            "Payrolls retrieved successfully.");
    }

    public function show(Company $company, int $payrollId)
    {
        $payroll = $company->payrolls()
            ->where('type', PayrollEnumerator::TYPE_SPECIAL)
            ->findOrFail($payrollId);
        $payroll->load('employee');
        return $this->sendResponse(new PayrollResource($payroll), 'Payroll retrieved successfully.');
    }

    public function store(SpecialPayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employees = $this->specialPayrollStrategy->getEmployees($company, $input);
        $payroll = $this->specialPayrollStrategy->generate($employees, $input);
        return $this->sendResponse(PayrollResource::collection($payroll), "Special payroll generated successfully.");
    }

    public function update(UpdatePayrollRequest $request, Company $company, int $payrollId): JsonResponse
    {
        $input = $request->validated();
        $payroll = $this->specialPayrollStrategy->update($company, $payrollId, $input);
        return $this->sendResponse(new PayrollResource($payroll), "Special payroll updated successfully.");
    }
}
