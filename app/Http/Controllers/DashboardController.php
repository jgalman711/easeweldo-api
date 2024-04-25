<?php

namespace App\Http\Controllers;

use App\Http\Resources\PeriodResource;
use App\Models\Company;
use App\Services\CompanyAttendanceService;
use App\Services\EmployeeService;
use App\Services\PeriodService;
use Illuminate\Http\JsonResponse;

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

    public function __invoke(Company $company): JsonResponse
    {
        $employees = $this->employeeService->generateDashboardDetails($company);
        $period = $this->periodService->generateDashboardDetails($company);
        $attendance = $this->companyAttendanceService->getAttendanceSummaryByWeek($company);
        $latestDisbursements = $company->getCompletedRegularDisbursements(6);
        return $this->sendResponse([
            ...$period,
            ...$employees,
            'attendance_summary' => $attendance,
            'disbursement_history' => PeriodResource::collection($latestDisbursements)
        ], 'Dashboard data retrieved successfully.');
    }
}
