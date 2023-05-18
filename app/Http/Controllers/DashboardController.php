<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Holiday;
use App\Services\LeaveService;
use App\Services\TimeRecordService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected $leaveService;

    protected $timeRecordService;

    public function __construct(LeaveService $leaveService, TimeRecordService $timeRecordService)
    {
        $this->leaveService = $leaveService;
        $this->timeRecordService = $timeRecordService;
    }

    public function dashboard(Company $company): JsonResponse
    {
        $lates = 0;
        $absents = 0;
        $noWorkSchedules = [];
        foreach ($company->employees as $employee) {
            try {
                list($expectedClockIn, $expectedClockOut) = $this->timeRecordService->getExpectedScheduleOf($employee);
                if ($expectedClockIn && $expectedClockOut) {
                    $timeRecord = $employee->timeRecords()->whereDate('created_at', date('Y-m-d'))->first();
                    if (optional($timeRecord)->clock_in > $expectedClockIn) {
                        $lates++;
                    } elseif (optional($timeRecord)->clock_in == null) {
                        $absents++;
                    }
                }
            } catch (Exception $e) {
                array_push($noWorkSchedules, $employee->fullName);
            }
        }

        $upcomingHolidays = Holiday::whereDate('date', '>=', now())
            ->whereDate('date', '<=', now()->addDays(7))
            ->orderBy('date')
            ->first();
            
        $dashboardData = [
            "lates" => $lates,
            "absents" => $absents,
            "upcoming_holidays" => $upcomingHolidays,
            "no_work_schedule" => $noWorkSchedules
        ];

        $leavesCollection = $this->leaveService->getSoonestLeaves($company->id);
        if ($leavesCollection->first()) {
            $dashboardData['upcoming_leaves'] = [
                'count' => $leavesCollection->first()->count(),
                'date' => $leavesCollection->keys()->first()
            ];
        }
        return $this->sendResponse($dashboardData, 'Dashboard data retrieved successfully.');
    }
}
