<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class UserTemporaryPasswordResetController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function update(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->employees()->findOrFail($employeeId);
        $user = $this->authService->temporaryPasswordReset($employee->user);
        return $this->sendResponse(
            new EmployeeResource($employee),
            "User's temporary password: {$user->temporary_password}. It expires after an hour. Please change it upon login."
        );
    }
}
