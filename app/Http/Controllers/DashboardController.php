<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\EmployeeService;
use App\Services\PeriodService;

class DashboardController extends Controller
{
    protected $employeeService;
    protected $periodService;

    public function __construct(EmployeeService $employeeService, PeriodService $periodService)
    {
        $this->employeeService = $employeeService;
        $this->periodService = $periodService;
    }

    public function index(Company $company)
    {
        $employees = $this->employeeService->generateDashboardDetails($company);
        $period = $this->periodService->generateDashboardDetails($company);
        return [
            ...$period,
            ...$employees
        ];
    }
}
