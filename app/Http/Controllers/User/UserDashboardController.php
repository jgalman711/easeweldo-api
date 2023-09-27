<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Company;
use App\Services\TimeRecordService;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function index(Request $request, Company $company, int $employeeId)
    {
        $employee = $company->employees()->find($employeeId);
        $timeRecord = $this->timeRecordService->getTimeRecordToday($employee);
        $workSchedule = $employee->schedules->first();
        $data = [
            'timeRecord' => $timeRecord,
            'workSchedule' => $workSchedule
        ];

        return $this->sendResponse(new DashboardResource($data), 'Dashboard data successfully retrieved.');
    }
}
