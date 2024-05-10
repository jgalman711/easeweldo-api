<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\LeaveResource;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Company $company, Employee $employee): JsonResponse
    {
        return $this->sendResponse(LeaveResource::collection($employee->leaves), 'Employee leaves retrieved successfully.');
    }
}
