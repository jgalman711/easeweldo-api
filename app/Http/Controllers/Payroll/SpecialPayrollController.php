<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialPayrollRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class SpecialPayrollController extends Controller
{
    protected $specialPayrollStrategy;

    public function __construct()
    {
        $this->specialPayrollStrategy = PayrollStrategyFactory::createStrategy(PayrollEnumerator::TYPE_SPECIAL);
    }

    public function store(SpecialPayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employee = $company->employees()->find($input['employee_id']);
        if (!$employee) {
            return $this->sendError('Employee not found.');
        }
        $payroll = $this->specialPayrollStrategy->generate($employee, $input);
        return $this->sendResponse($payroll, "Special payroll generated successfully.");
    }
}
