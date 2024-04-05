<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payroll\BasePayrollResource;
use App\Models\Company;
use App\Models\Employee;
use App\Traits\PayrollFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPayrollController extends Controller
{
    use PayrollFilter;
    
    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/payrolls",
     *     summary="List Employee Schedules",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Payroll"},
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
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order (e.g., name)",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Payrolls retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No payroll found"
     *     )
     * )
     */
    public function index(Request $request, Employee $employee): JsonResponse
    {
        $payrolls = $this->applyFilters($request, $employee->payrolls()->where('type', 'regular')->where('status', 'paid'));
        if ($payrolls) {
            return $this->sendResponse(BasePayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
        } else {
            return $this->sendError('Payrolls not found');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/payrolls/{payroll-id}",
     *     summary="Get Employee Payroll",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Payroll"},
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
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="payroll-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee schedule or 'latest' to get the latest schedule",
     *
     *         @OA\Schema(type="integer"),
     *     ),
     *
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         required=false,
     *         description="Format for the payroll view",
     *
     *         @OA\Schema(type="string", enum={"default", "details"})
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Employee Payroll retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee Schedule retrieved successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="404",
     *         description="Employee Payroll not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Employee Payroll not found")
     *         )
     *     )
     * )
     */
    public function show(Request $request, Company $company, int $employeeId, int $payrollId): JsonResponse
    {
        $payroll = $company->employees()->find($employeeId)->payrolls()->find($payrollId);
        if ($payroll) {
            $payrollResource = $this->payrollService->format($payroll, $request->format);

            return $this->sendResponse($payrollResource, 'Payroll retrieved successfully.');
        } else {
            return $this->sendError('Payroll not found');
        }
    }
}
