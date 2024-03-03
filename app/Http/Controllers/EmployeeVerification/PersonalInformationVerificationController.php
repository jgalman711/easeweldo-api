<?php

namespace App\Http\Controllers\EmployeeVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeVerificationRequest\PersonalInformationRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class PersonalInformationVerificationController extends Controller
{
    public function __invoke(PersonalInformationRequest $request, Company $company): JsonResponse
    {
        return $this->sendResponse(["data" => $request->validated()], "Personal Information is valid.");
    }
}
