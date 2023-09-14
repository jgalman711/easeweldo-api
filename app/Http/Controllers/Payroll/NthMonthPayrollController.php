<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\NthMonthPayRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class NthMonthPayrollController extends Controller
{
    protected $nthMonthPayrollStrategy;

    public function __construct()
    {
        $this->nthMonthPayrollStrategy = PayrollStrategyFactory::createStrategy(PayrollEnumerator::TYPE_NTH_MONTH_PAY);
    }

    public function store(NthMonthPayRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $payrolls = $this->nthMonthPayrollStrategy->generate($company, $input);
        dd($payrolls);
        return $this->sendResponse(PayrollResource::collection($payrolls),
            "{$input['description']} generated successfully.");
    }
}
