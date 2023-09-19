<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\UserEmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadEmployeeController extends Controller
{
    protected $userEmployeeService;

    public function __construct(UserEmployeeService $userEmployeeService)
    {
        $this->userEmployeeService = $userEmployeeService;
    }

    public function store(Company $company, Request $request): JsonResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $message = "Unable to upload all employees.";
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $employeesData = array_map('str_getcsv', file($path));
            list($employees, $errors) = $this->userEmployeeService->bulk($company, $employeesData);
            if (!empty($employees) && !empty($errors)) {
                $message = "Unable to upload some of the employees.";
            } elseif (!empty($employees) && empty($errors)) {
                return $this->sendResponse(
                    BaseResource::collection([
                        "success" => $employees,
                        "errors" => $errors
                    ]),
                    "Employees uploaded successfully."
                );
            }
        }
        return $this->sendError(BaseResource::collection([
            "success" => $employees,
            "errors" => $errors
        ]), $message);
    }
}
