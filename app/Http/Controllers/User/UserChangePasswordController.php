<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class UserChangePasswordController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function update(ChangePasswordRequest $request, Company $company, int $employeeId): JsonResponse
    {
        $data = $request->validated();
        $employee = $company->employees()->findOrFail($employeeId);
        $this->authService->changePassword($employee->user, $data);
        return $this->sendResponse(new EmployeeResource($employee), 'Password changed successfully.');
    }
}
