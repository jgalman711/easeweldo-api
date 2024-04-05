<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CompanyAttendanceService;
use App\Services\EmployeeService;
use App\Services\PeriodService;

class DashboardController extends Controller
{
    protected $companyAttendanceService;

    protected $employeeService;

    protected $periodService;

    public function __construct(
        CompanyAttendanceService $companyAttendanceService,
        EmployeeService $employeeService,
        PeriodService $periodService
    ) {
        $this->companyAttendanceService = $companyAttendanceService;
        $this->employeeService = $employeeService;
        $this->periodService = $periodService;
    }

    public function __invoke(Company $company)
    {
        $employees = $this->employeeService->generateDashboardDetails($company);
        $period = $this->periodService->generateDashboardDetails($company);
        $attendance = $this->companyAttendanceService->getAttendanceSummaryByWeek($company);

        return [
            ...$period,
            ...$employees,
            'attendance_summary' => $attendance,
        ];
    }
}
