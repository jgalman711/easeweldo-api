<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function dashboard(Company $company): JsonResponse
    {
        // $employees = $company->employees;
        return $this->sendResponse(BaseResource::collection($employees), 'Employees retrieved successfully.');
    }
}
