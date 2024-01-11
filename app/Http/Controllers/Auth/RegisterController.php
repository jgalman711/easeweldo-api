<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRegistrationRequest;
use App\Http\Resources\BaseResource;
use App\Services\RegistrationService;

class RegisterController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user and company",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="company_name", type="string", description="Name of the company", maxLength=255),
     *                 @OA\Property(property="email_address", type="string", format="email", description="User's email address", maxLength=255),
     *                 @OA\Property(property="password", type="string", format="password", description="User's password", minLength=6),
     *                 @OA\Property(property="password_confirmation", type="string", format="password", description="Confirm password"),
     *                 @OA\Property(property="g-recaptcha-response", type="string", description="reCAPTCHA response token obtained from the frontend"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="User and company registered successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="User and company registered successfully."
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="company_name", type="string", example="Example Company"),
     *                     @OA\Property(property="email_address", type="string", example="user@example.com"),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="token", type="string", example="123|ABC"),
     *                 )
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="429", description="Too Many Requests - reCAPTCHA verification failed"),
     * )
     */
    public function register(CompanyRegistrationRequest $request)
    {
        $input = $request->validated();
        $result = $this->registrationService->register($input);
        return $this->sendResponse(new BaseResource($result), 'Company registered successfully.');
    }
}
