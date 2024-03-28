<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResource;
use App\Services\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate access token",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                      property="email_address",
     *                      type="string",
     *                      format="email",
     *                      description="User's email address"
     *                 ),
     *                 @OA\Property(property="password", type="string", description="User's password"),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Authentication successful",
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="User login successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="token", type="string", description="Generated access token"),
     *                     @OA\Property(property="username", type="string", description="User's username"),
     *                     @OA\Property(
     *                          property="first_name",
     *                          type="string",
     *                          nullable=true,
     *                          description="User's first name"
     *                    ),
     *                     @OA\Property(
     *                          property="last_name",
     *                          type="string",
     *                          nullable=true,
     *                          description="User's last name"
     *                     ),
     *                     @OA\Property(property="status", type="string", description="User's status"),
     *                     @OA\Property(property="email", type="string", description="User's email"),
     *                     @OA\Property(property="email_address", type="string", description="User's email address"),
     *                     @OA\Property(
     *                          property="email_verified_at",
     *                          type="string",
     *                          nullable=true,
     *                          description="Timestamp when email was verified"
     *                     ),
     *                     @OA\Property(
     *                          property="companies",
     *                          type="array",
     *                          description="User's associated companies",
     *
     *                          @OA\Items()
     *                     ),
     *
     *                     @OA\Property(
     *                          property="employee",
     *                          type="object",
     *                          description="User's associated employee details"
     *                     ),
     *                     @OA\Property(property="roles", type="array", description="User's roles", @OA\Items(
     *                         @OA\Property(property="id", type="integer", description="Role ID"),
     *                         @OA\Property(property="name", type="string", description="Role name"),
     *                         @OA\Property(property="guard_name", type="string", description="Role guard name")
     *                     )),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="401", description="Unauthorized"),
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'email_address', 'password']);
        try {
            $user = $this->loginService->login($credentials, $request->remember);
            $message = $this->loginService->getSuccessMessage($user);

            return $this->sendResponse(new LoginResource($user), $message);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return $this->sendResponse($user, 'User successfully logged out.');
    }
}
