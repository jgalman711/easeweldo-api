<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\UserEmployeeService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected $employeeService;

    protected $userService;

    protected $userEmployeeService;

    public function __construct(
        EmployeeService $employeeService,
        UserService $userService,
        UserEmployeeService $userEmployeeService
    ) {
        $this->employeeService = $employeeService;
        $this->userService = $userService;
        $this->userEmployeeService = $userEmployeeService;
        $this->setCacheIdentifier('employees');
    }
    
    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees",
     *     summary="Get a list of employees for a specific company",
     *     security={{"bearerAuth":{}}},
     *     tags={"Company Employees"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order for the results (e.g., first_name, -last_name)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering results",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List of employees retrieved successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Employees retrieved successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="first_name", type="string", example="John"),
     *                         @OA\Property(property="last_name", type="string", example="Doe"),
     *                         @OA\Property(property="job_title", type="string", example="Developer"),
     *                         @OA\Property(property="employment_status", type="string", example="Full-time"),
     *                         @OA\Property(property="department", type="string", example="IT")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", description="Company not found")
     * )
     */
    public function index(Request $request, Company $company): JsonResponse
    {
        $employees = $this->applyFilters($request, $company->employees()->with('user'), [
            'user.first_name',
            'user.last_name',
            'job_title',
            'employment_status',
            'department'
        ]);
        return $this->sendResponse(EmployeeResource::collection($employees), 'Employees retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/employees",
     *     summary="Create a new employee for a specific company",
     *     security={{"bearerAuth":{}}},
     *     tags={"Company Employees"},
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
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="user_id", type="integer", nullable=true, description="User ID associated with the employee (optional)"),
     *                 @OA\Property(property="first_name", type="string", description="First name of the employee", example="John"),
     *                 @OA\Property(property="last_name", type="string", description="Last name of the employee", example="Doe"),
     *                 @OA\Property(property="employee_number", type="string", nullable=true, description="Employee number", example="E12345"),
     *                 @OA\Property(property="department", type="string", description="Department of the employee", example="IT"),
     *                 @OA\Property(property="job_title", type="string", description="Job title of the employee", example="Developer"),
     *                 @OA\Property(property="date_of_hire", type="string", format="date", description="Date of hire", example="2024-01-15"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", description="Date of birth", example="1990-01-15"),
     *                 @OA\Property(property="status", type="string", nullable=true, description="Employment status (optional)", enum={"regular", "inactive", "on-leave"}),
     *                 @OA\Property(property="employment_status", type="string", nullable=true, description="Employment status (optional)", enum={"regular", "probationary", "resigned", "terminated"}),
     *                 @OA\Property(property="employment_type", type="string", nullable=true, description="Employment type (optional)", enum={"full-time", "part-time", "contract"}),
     *                 @OA\Property(property="working_days_per_week", type="integer", nullable=true, description="Number of working days per week (optional)"),
     *                 @OA\Property(property="working_hours_per_day", type="integer", nullable=true, description="Number of working hours per day (optional)"),
     *                 @OA\Property(property="email_address", type="string", format="email", nullable=true, description="Email address (optional)"),
     *                 @OA\Property(property="mobile_number", type="string", nullable=true, description="Mobile number (optional)", format="PH_MOBILE_NUMBER"),
     *                 @OA\Property(property="address_line", type="string", description="Address line", example="123 Main St"),
     *                 @OA\Property(property="barangay_town_city_province", type="string", description="Barangay, town, city, or province", example="Cityville"),
     *                 @OA\Property(property="date_of_termination", type="string", format="date", nullable=true, description="Date of termination (optional)", example="2025-01-15"),
     *                 @OA\Property(property="sss_number", type="string", nullable=true, description="SSS (Social Security System) number (optional)"),
     *                 @OA\Property(property="pagibig_number", type="string", nullable=true, description="Pag-IBIG (Home Development Mutual Fund) number (optional)"),
     *                 @OA\Property(property="philhealth_number", type="string", nullable=true, description="PhilHealth number (optional)"),
     *                 @OA\Property(property="tax_identification_number", type="string", nullable=true, description="Tax Identification Number (optional)"),
     *                 @OA\Property(property="bank_name", type="string", nullable=true, description="Bank name (optional)"),
     *                 @OA\Property(property="bank_account_name", type="string", nullable=true, description="Bank account name (optional)"),
     *                 @OA\Property(property="bank_account_number", type="string", nullable=true, description="Bank account number (optional)"),
     *                 @OA\Property(property="profile_picture", type="string", format="binary", nullable=true, description="Profile picture image file (optional, max size: 2048 bytes, allowed formats: jpeg, png, jpg, gif, svg)"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Employee created successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function store(EmployeeRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        try {
            list($employee) = $this->userEmployeeService->create($company, $input);
            return $this->sendResponse(new EmployeeResource($employee), "Employee created successfully.");
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}",
     *     summary="Get details of a specific employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Company Employees"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Employee details retrieved successfully"),
     *     @OA\Response(response="404", description="Employee not found")
     * )
     */
    public function show(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        return $this->sendResponse(new EmployeeResource($employee), 'Employee retrieved successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{company-slug}/employees/{employee-id}",
     *     summary="Update details of a specific employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Company Employees"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="user_id", type="integer", nullable=true, description="User ID associated with the employee (optional)"),
     *                 @OA\Property(property="first_name", type="string", description="First name of the employee", example="John"),
     *                 @OA\Property(property="last_name", type="string", description="Last name of the employee", example="Doe"),
     *                 @OA\Property(property="employee_number", type="string", nullable=true, description="Employee number", example="E12345"),
     *                 @OA\Property(property="department", type="string", description="Department of the employee", example="IT"),
     *                 @OA\Property(property="job_title", type="string", description="Job title of the employee", example="Developer"),
     *                 @OA\Property(property="date_of_hire", type="string", format="date", description="Date of hire", example="2024-01-15"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", description="Date of birth", example="1990-01-15"),
     *                 @OA\Property(property="status", type="string", nullable=true, description="Employment status (optional)", enum={"regular", "inactive", "on-leave"}),
     *                 @OA\Property(property="employment_status", type="string", nullable=true, description="Employment status (optional)", enum={"regular", "probationary", "resigned", "terminated"}),
     *                 @OA\Property(property="employment_type", type="string", nullable=true, description="Employment type (optional)", enum={"full-time", "part-time", "contract"}),
     *                 @OA\Property(property="working_days_per_week", type="integer", nullable=true, description="Number of working days per week (optional)"),
     *                 @OA\Property(property="working_hours_per_day", type="integer", nullable=true, description="Number of working hours per day (optional)"),
     *                 @OA\Property(property="email_address", type="string", format="email", nullable=true, description="Email address (optional)"),
     *                 @OA\Property(property="mobile_number", type="string", nullable=true, description="Mobile number (optional)", format="PH_MOBILE_NUMBER"),
     *                 @OA\Property(property="address_line", type="string", description="Address line", example="123 Main St"),
     *                 @OA\Property(property="barangay_town_city_province", type="string", description="Barangay, town, city, or province", example="Cityville"),
     *                 @OA\Property(property="date_of_termination", type="string", format="date", nullable=true, description="Date of termination (optional)", example="2025-01-15"),
     *                 @OA\Property(property="sss_number", type="string", nullable=true, description="SSS (Social Security System) number (optional)"),
     *                 @OA\Property(property="pagibig_number", type="string", nullable=true, description="Pag-IBIG (Home Development Mutual Fund) number (optional)"),
     *                 @OA\Property(property="philhealth_number", type="string", nullable=true, description="PhilHealth number (optional)"),
     *                 @OA\Property(property="tax_identification_number", type="string", nullable=true, description="Tax Identification Number (optional)"),
     *                 @OA\Property(property="bank_name", type="string", nullable=true, description="Bank name (optional)"),
     *                 @OA\Property(property="bank_account_name", type="string", nullable=true, description="Bank account name (optional)"),
     *                 @OA\Property(property="bank_account_number", type="string", nullable=true, description="Bank account number (optional)"),
     *                 @OA\Property(property="profile_picture", type="string", format="binary", nullable=true, description="Profile picture image file (optional, max size: 2048 bytes, allowed formats: jpeg, png, jpg, gif, svg)"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Employee details updated successfully"),
     *     @OA\Response(response="404", description="Employee not found"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function update(Request $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $employee = $company->getEmployeeById($employeeId);
            $employee = $this->employeeService->update($request, $company, $employee);
            DB::commit();
            $this->forget($company, $employee->id);
            return $this->sendResponse(new EmployeeResource($employee), 'Employee updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/companies/{company-slug}/employees/{employee-id}",
     *     summary="Delete a specific employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Company Employees"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Employee deleted successfully"),
     *     @OA\Response(response="404", description="Employee not found")
     * )
     */
    public function destroy(Company $company, Employee $employee): JsonResponse
    {
        $company->getEmployeeById($employee->id);
        $employee->delete();
        $this->forget($company, $employee->id);
        return $this->sendResponse(new EmployeeResource($employee), 'Employee deleted successfully.');
    }

    public function all(Request $request): JsonResponse
    {
        $employees = $this->applyFilters($request, Employee::with(['company:id,name,slug,status']), [
            'first_name',
            'last_name',
            'employment_status',
            'company.name'
        ]);
        return $this->sendResponse(EmployeeResource::collection($employees), 'Employees retrieved successfully.');
    }
}
