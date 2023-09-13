<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRegistrationRequest;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use App\Services\RegistrationService;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function register(CompanyRegistrationRequest $request)
    {
        $input = $request->validated();
        $result = $this->registrationService->register($input);
        return $this->sendResponse($result, 'Company registered successfully.');
    }
}
