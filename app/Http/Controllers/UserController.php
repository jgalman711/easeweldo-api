<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\User;
use App\Services\QrService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    protected $qrService;

    public function __construct(UserService $userService, QrService $qrService)
    {
        $this->userService = $userService;
        $this->qrService = $qrService;
    }

    public function index(Request $request): JsonResponse
    {
        $users = $this->applyFilters($request, User::query(), [
            'email_address',
            'username',
            'first_name',
            'last_name',
            'companies.name',
            'companies.legal_name',
            'companies.status'
        ]);
        return $this->sendResponse(BaseResource::collection($users), 'Users retrieved successfully.');
    }

    public function show(User $user): JsonResponse
    {
        return $this->sendResponse(new BaseResource($user), 'User retrieved successfully.');
    }

    public function store(UserRequest $userRequest)
    {
        $input = $userRequest->validated();
        $company = Company::findOrFail($input['company_id']);
        $user = $this->userService->create($company, $input);
        return $this->sendResponse(
            new BaseResource($user),
            "User created successfully. This is the user's temporary password: {$user->temporary_password}"
        );
    }

    public function qrcode(): Response
    {
        $user = Auth::user();
        $employee = $user->employee;
        $company = $employee->company;
        $data = $this->qrService->generate($company->id, $employee->id);
        return response($data)->header('Content-type', 'image/png');
    }
}
