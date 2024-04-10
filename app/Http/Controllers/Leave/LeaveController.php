<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Models\Company;
use App\Models\Leave;
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

    public function index(Request $request, Company $company): JsonResponse
    {
        $leaves = $this->applyFilters($request, $company->leaves()->with('employee.user', 'employee.supervisor'));
        return $this->sendResponse(LeaveResource::collection($leaves), 'Leaves retrieved successfully.');
    }

    public function store(LeaveRequest $leaveRequest, Company $company): JsonResponse
    {
        $input = $leaveRequest->validated();
        $employee = $company->employees()->findOrFail($input['employee_id']);
        $employee->setRelation('company', $company);
        try {
            $leaves = $this->leaveService->apply($employee, $input);
            return $this->sendResponse(LeaveResource::collection($leaves), 'Leaves created successfully.');
        } catch (Exception $e) {
            return $this->sendError('Unable to apply leave.', $e->getMessage());
        }
    }

    public function show(Company $company, int $leaveId): JsonResponse
    {
        $leave = $company->leaves()->findOrFail($leaveId);
        return $this->sendResponse(new LeaveResource($leave), 'Leave retrieved successfully');
    }

    public function update(LeaveRequest $leaveRequest, Company $company, int $leaveId): JsonResponse
    {
        $input = $leaveRequest->validated();
        $leave = Leave::where([
            'id' => $leaveId,
            'company_id' => $company->id,
            'employee_id' => $input['employee_id']
        ])->firstOrFail();
        $leave->update($input);
        return $this->sendResponse(new LeaveResource($leave), 'Leave updated successfully.');
    }

    public function destroy(Company $company, int $leaveId): JsonResponse
    {
        $leave = Leave::where([
            'id' => $leaveId,
            'company_id' => $company->id
        ])->firstOrFail();
        $leave->delete();
        return $this->sendResponse(new LeaveResource($leave), 'Leave deleted successfully');
    }
}
