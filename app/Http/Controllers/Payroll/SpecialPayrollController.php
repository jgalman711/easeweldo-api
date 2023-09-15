<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialPayrollRequest;
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
        $specialPayroll = $this->applyFilters($request, $company->payrolls());
        return $this->sendResponse(PayrollResource::collection($specialPayroll),
            "Payrolls retrieved successfully.");
    }

    public function store(SpecialPayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employees = $this->specialPayrollStrategy->getEmployees($company, $input);
        $payroll = $this->specialPayrollStrategy->generate($employees, $input);
        return $this->sendResponse($payroll, "Special payroll generated successfully.");
    }
}
