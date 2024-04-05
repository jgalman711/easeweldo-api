<?php

namespace App\Http\Controllers;

use App\Http\Requests\OvertimeRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\OvertimeRequest as ModelsOvertimeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeRequestController extends Controller
{
    public function index(Request $request, Company $company)
    {
        $overtimeRequests = $this->applyFilters($request, $company->overtimeRequests());

        return $this->sendResponse(BaseResource::collection($overtimeRequests), 'Employees retrieved successfully.');
    }

    public function store(OvertimeRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $input['company_id'] = $company->id;
        $input['created_by'] = Auth::id();
        $input['submitted_date'] = now();
        $employee = $company->employees()->where('company_employee_id', $request->employee_id)->first();
        if ($employee) {
            $overtime = ModelsOvertimeRequest::create($input);

            return $this->sendResponse($overtime, 'Overtime request successfully created.');
        } else {
            return $this->sendError('Employee not found.');
        }
    }
}
