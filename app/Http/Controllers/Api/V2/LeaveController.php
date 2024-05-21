<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\EmployeeLeaveRequest;
use App\Http\Resources\V2\LeaveResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index(Company $company, Employee $employee): JsonResponse
    {
        $leaves = $employee->load('supervisor')->leaves()->paginate();
        $leaves->getCollection()->each->setRelation('employee', $employee);
        $leaves->getCollection()->each->setRelation('company', $company);
        return $this->sendResponse(LeaveResource::collection($leaves), 'Employee leaves retrieved successfully.');
    }

    public function show(Company $company, Employee $employee, int $leaveId): JsonResponse
    {
        $leave = $employee->load('supervisor')->leaves()->findOrFail($leaveId);
        $leave->setRelation('employee', $employee);
        $leave->setRelation('company', $company);
        return $this->sendResponse(new LeaveResource($leave), 'Employee leave retrieved successfully.');
    }

    public function store(EmployeeLeaveRequest $request, Company $company, Employee $employee): JsonResponse
    {
        $input = $request->validated();
        $this->leaveService->apply($employee, $input);
        return $this->sendMessage('Leaves created successfully.');
    }

    public function update(EmployeeLeaveRequest $request, Company $company, Employee $employee, int $leaveId): JsonResponse
    {
        $input = $request->validated();
        $leave = $employee->leaves()->findOrFail($leaveId);
        $leave->update($input);
        return $this->sendResponse(new LeaveResource($leave), 'Leave updated successfully.');
    }
}
