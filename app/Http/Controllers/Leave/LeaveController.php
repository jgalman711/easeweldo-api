<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\V2\LeaveResource as V2LeaveResource;
use App\Models\Company;
use App\Models\Leave;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function show(Company $company, int $leaveId): JsonResponse
    {
        $leave = $company->leaves()->findOrFail($leaveId);
        return $this->sendResponse(new LeaveResource($leave), 'Leave retrieved successfully.');
    }

    public function store(LeaveRequest $request, Company $company)
    {
        $input = $request->validated();
        $leaves = $this->leaveService->apply($input);
        return $this->sendResponse(V2LeaveResource::collection($leaves), 'Leave created successfully.');
    }
}
