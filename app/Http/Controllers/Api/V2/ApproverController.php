<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\ApproverRequest;
use App\Http\Resources\V2\ApproverResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\V2\ApproverService;
use Illuminate\Http\JsonResponse;

class ApproverController extends Controller
{
    protected $approverService;

    public function __construct(ApproverService $approverService)
    {
        $this->approverService = $approverService;
    }

    public function index(Company $company, Employee $employee): JsonResponse
    {
        $approvers = $employee->approvers()
            ->with('user')
            ->withPivot('order', 'request_type')
            ->orderBy('order', 'asc')
            ->get();
        $approvers->each->setRelation('company', $company);
        return $this->sendResponse(ApproverResource::collection($approvers), 'Approvers retrieved successfully.');
    }

    public function show(Company $company, Employee $employee, int $approverId): JsonResponse
    {
        $approver = $employee->approvers()->where('employee_id', $approverId)
                        ->with('user')
                        ->withPivot('order', 'request_type')
                        ->firstOrFail();
        $approver->setRelation('company', $company);
        return $this->sendResponse(new ApproverResource($approver), 'Approver retrieved successfully.');
    }

    public function store(ApproverRequest $request, Company $company, Employee $employee): JsonResponse
    {
        $input = $request->validated();
        $input['requester_employee_id'] = $employee->id;
        $approver = $this->approverService->createOrUpdate($input);
        $approver->setRelation('requester', $employee);
        return $this->sendMessage('Approver created successfully.');
    }
}
