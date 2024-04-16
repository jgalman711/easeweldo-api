<?php

namespace App\Http\Controllers\EmployeeVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeVerificationRequest\SalaryComputationRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class SalaryDetailsVerificationController extends Controller
{
    public function __invoke(SalaryComputationRequest $request, Company $company): JsonResponse
    {
        return $this->sendResponse(['data' => $request->validated()], 'Salary details are validated successfully');
    }
}
