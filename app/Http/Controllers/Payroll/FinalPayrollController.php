<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinalPayrollRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
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
        $nthMonthPayroll = $this->applyFilters($request, $company->payrolls());
        return $this->sendResponse(BaseResource::collection($nthMonthPayroll),
            "Payrolls retrieved successfully.");
    }

    public function store(FinalPayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employees = $this->finalPayrollStrategy->getEmployees($company, $input);
        list($payrolls, $errors) = $this->finalPayrollStrategy->generate($employees, $input);
        return $this->sendResponse(BaseResource::collection([
            'success' => $payrolls,
            'failed' => $errors
        ]), "Final payroll generated successfully.");
    }

}
