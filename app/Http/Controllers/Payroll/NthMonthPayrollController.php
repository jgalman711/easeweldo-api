<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\NthMonthPayRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
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
}
