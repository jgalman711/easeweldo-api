<?php

namespace App\Http\Controllers\EmployeeVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeVerificationRequest\OtherDetailsRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class OtherDetailsVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/verify/other-details",
     *     summary="Verify employee creation employee details",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Creation Verification"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="sss_number", type="string", nullable=true, description="SSS (Social Security System) number (optional)"),
     *                 @OA\Property(property="pagibig_number", type="string", nullable=true, description="Pag-IBIG (Home Development Mutual Fund) number (optional)"),
     *                 @OA\Property(property="philhealth_number", type="string", nullable=true, description="PhilHealth number (optional)"),
     *                 @OA\Property(property="tax_identification_number", type="string", nullable=true, description="Tax Identification Number (optional)"),
     *                 @OA\Property(property="bank_name", type="string", nullable=true, description="Bank name (optional)"),
     *                 @OA\Property(property="bank_account_name", type="string", nullable=true, description="Bank account name (optional)"),
     *                 @OA\Property(property="bank_account_number", type="string", nullable=true, description="Bank account number (optional)")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Other details are validated successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function __invoke(OtherDetailsRequest $request, Company $company): JsonResponse
    {
        return $this->sendResponse(["data" => $request->validated()], "Other details are validated successfully");
    }
}
