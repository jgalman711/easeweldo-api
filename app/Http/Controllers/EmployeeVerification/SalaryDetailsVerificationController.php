<?php

namespace App\Http\Controllers\EmployeeVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeVerificationRequest\SalaryComputationRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class SalaryDetailsVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/verify/salary-details",
     *     summary="Verify employee creation salary details",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Creation Verification"},
     *
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object"
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(response="201", description="Salary details are validated successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function __invoke(SalaryComputationRequest $request, Company $company): JsonResponse
    {
        return $this->sendResponse(['data' => $request->validated()], 'Salary details are validated successfully');
    }
}
