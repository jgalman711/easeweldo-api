<?php

namespace App\Http\Controllers;

use App\Traits\Cache;
use App\Traits\CompanyEmployee;
use App\Traits\Filter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;

class Controller
{
    use AuthorizesRequests, ValidatesRequests, CompanyEmployee, Filter, Cache;

    public const ADMIN_CACHE_KEY = 'admin';

    /**
     * @OA\Info(
     *    title="Easeweldo API Documentation",
     *    version="1.0.0",
     * )
     * @OA\SecurityScheme(
     *     type="http",
     *     securityScheme="bearerAuth",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     */
    public function sendResponse($result, $message): JsonResponse
    {
        $data = is_array($result) ? $result : $result->response()->getData(true);
        $response = [
            'success' => true,
            'message' => $message,
            ...$data,
        ];


        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
