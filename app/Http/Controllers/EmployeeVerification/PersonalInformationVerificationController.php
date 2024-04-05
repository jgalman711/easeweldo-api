<?php

namespace App\Http\Controllers\EmployeeVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeVerificationRequest\PersonalInformationRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class PersonalInformationVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/verify/personal-information",
     *     summary="Verify employee creation personal information",
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
     *                 type="object",
     *
     *                 @OA\Property(property="first_name", type="string", description="First name of the employee", example="John"),
     *                 @OA\Property(property="last_name", type="string", description="Last name of the employee", example="Doe"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", description="Date of birth", example="1990-01-15"),
     *                 @OA\Property(property="email_address", type="string", format="email", nullable=true, description="Email address (optional)"),
     *                 @OA\Property(property="mobile_number", type="string", nullable=true, description="Mobile number (optional)", format="PH_MOBILE_NUMBER"),
     *                 @OA\Property(property="address_line", type="string", description="Address line", example="123 Main St"),
     *                 @OA\Property(property="barangay_town_city_province", type="string", description="Barangay, town, city, or province", example="Cityville"),
     *                 @OA\Property(property="profile_picture", type="string", format="binary", nullable=true, description="Profile picture image file (optional, max size: 2048 bytes, allowed formats: jpeg, png, jpg, gif, svg)"),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(response="201", description="Personal Information is validated successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function __invoke(PersonalInformationRequest $request, Company $company): JsonResponse
    {
        return $this->sendResponse(['data' => $request->validated()], 'Personal Information is validated successfully');
    }
}
