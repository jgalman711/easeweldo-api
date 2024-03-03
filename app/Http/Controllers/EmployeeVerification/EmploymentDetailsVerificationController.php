<?php

namespace App\Http\Controllers\EmployeeVerification;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeVerificationRequest\EmployeeDetailsRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class EmploymentDetailsVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/verify/employee-details",
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *              @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="department", type="string", description="Department of the employee", example="IT"),
     *                 @OA\Property(property="job_title", type="string", description="Job title of the employee", example="Developer"),
     *                 @OA\Property(property="date_of_hire", type="string", format="date", description="Date of hire", example="2024-01-15"),
     *                 @OA\Property(property="employment_status", type="string", nullable=true, description="Employment status (optional)", enum={"regular", "probationary", "resigned", "terminated"}),
     *                 @OA\Property(property="employment_type", type="string", nullable=true, description="Employment type (optional)", enum={"full-time", "part-time", "contract"}),
     *                 @OA\Property(property="working_days_per_week", type="integer", nullable=true, description="Number of working days per week (optional)"),
     *                 @OA\Property(property="working_hours_per_day", type="integer", nullable=true, description="Number of working hours per day (optional)"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Employee details are validated successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function __invoke(EmployeeDetailsRequest $request, Company $company): JsonResponse
    {
        return $this->sendResponse(["data" => $request->validated()], "Employee details are validated successfully");
    }
}
