<?php

namespace App\Http\Controllers\EmployeeVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeVerificationRequest\EmployeeDetailsRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class EmploymentDetailsVerificationController extends Controller
{
    public function __invoke(EmployeeDetailsRequest $request, Company $company): JsonResponse
    {
        return $this->sendResponse(['data' => $request->validated()], 'Employee details are validated successfully');
    }
}
