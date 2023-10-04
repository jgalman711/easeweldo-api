<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\LeaveService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index(Request $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            $query = $company->leaves()->where('employee_id', $employeeId);
            $leaves = $this->leaveService->filter($request, $query);
            return $this->sendResponse($leaves, 'Leaves retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    public function store(LeaveRequest $leaveRequest, Company $company, int $employeeId): JsonResponse
    {
        $data = $leaveRequest->validated();
        try {
            $employee = $company->employees()->find($employeeId);
            $data['company_id'] = $company->id;
            $data['employee_id'] = $employeeId;
            $leave = $this->leaveService->apply($employee, $data);
            return $this->sendResponse(new BaseResource($leave), 'Leave created successfully.');
        } catch (Exception $e) {
            return $this->sendError("Unable to apply leave.", $e->getMessage());
        }
    }

    public function show(Company $company, int $employeeId, int $leaveId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $leave = $employee->getLeaveById($leaveId);
        return $this->sendResponse(new BaseResource($leave), 'Leave retrieved successfully');
    }

    public function update(LeaveRequest $leaveRequest, Company $company, int $employeeId, int $leaveId): JsonResponse
    {
        $input = $leaveRequest->validated();
        $employee = $company->getEmployeeById($employeeId);
        $leave = $employee->getLeaveById($leaveId);
        $leave->update($input);
        return $this->sendResponse(new BaseResource($leave), 'Leave updated successfully.');
    }

    public function destroy(Company $company, int $employeeId, int $leaveId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $leave = $employee->getLeaveById($leaveId);
        $leave->delete();
        return $this->sendResponse(new BaseResource($leave), 'Leave deleted successfully');
    }
}
