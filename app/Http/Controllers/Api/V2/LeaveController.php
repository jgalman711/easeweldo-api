<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\LeaveResource;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class LeaveController extends Controller
{
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
}
