<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportEmployeeRequest;
use App\Models\Company;
use App\Services\UserEmployeeService;

class ImportEmployeeController extends Controller
{
    protected $userEmployeeService;

    public function __construct(UserEmployeeService $userEmployeeService)
    {
        $this->userEmployeeService = $userEmployeeService;
    }

    public function __invoke(ImportEmployeeRequest $request, Company $company)
    {
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $employeesData = array_map('str_getcsv', file($path));
            list($employees, $errors) = $this->userEmployeeService->bulk($company, $employeesData);
        }
        
    }
}
