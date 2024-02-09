<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\EmployeeService;

class DashboardController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }
    public function index(Company $company)
    {
        $employees = $this->employeeService->generateDashboardDetails($company);
    }
}
