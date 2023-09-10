<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\BaseResource;
use App\Models\User;
use App\Services\QrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $qrService;

    public function __construct(QrService $qrService)
    {
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
