<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        $users = $this->applyFilters($request, User::query()->with('companies'), [
            'email_address',
            'username',
            'first_name',
            'last_name',
            'companies.name',
            'companies.legal_name',
            'companies.status'
        ]);
        return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');
    }

    public function show(User $user): JsonResponse
    {
        $user->load('companies');
        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }

    public function store(UserRequest $userRequest)
    {
        $input = $userRequest->validated();
        $companies = Company::whereIn('id', $input['company_id'])->get();
        $user = $this->userService->create($companies, $input);
        $user->load('companies');
        return $this->sendResponse(
            new UserResource($user),
            "User created successfully. This is the user's temporary password: {$user->temporary_password}. It expires after an hour. Please change it upon login."
        );
    }

    public function update(UserRequest $userRequest, User $user): JsonResponse
    {
        $input = $userRequest->validated();
        $user->update($input);
        $user->load('companies');
        return $this->sendResponse(new UserResource($user), "User updated successfully.");
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        $user->load('companies');
        return $this->sendResponse(new UserResource($user), "User deleted successfully.");
    }
}
