<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecialPayrollRequest;
use App\Models\Company;
use App\Services\Payroll\SpecialPayrollService;
use Illuminate\Http\JsonResponse;

class SpecialPayrollController extends Controller
{
    protected $specialPayrollService;

    public function __construct(SpecialPayrollService $specialPayrollService)
    {
        $this->specialPayrollService = $specialPayrollService;
    }

    public function store(SpecialPayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $employee = $company->employees()->find($input['employee_id']);
        if (!$employee) {
            return $this->sendError('Employee not found.');
        }
        $specialPayroll = $this->specialPayrollService->generate($employee, $input);
        return $this->sendResponse($specialPayroll, "Special payroll generated successfully.");
    }
}
