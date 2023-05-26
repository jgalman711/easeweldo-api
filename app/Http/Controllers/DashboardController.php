<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Holiday;
use App\Services\LeaveService;
use App\Services\PeriodService;
use App\Services\ReminderService;
use App\Services\TimeRecordService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $leaveService;

    protected $reminderService;

    protected $timeRecordService;

    protected $periodService;

    public function __construct(
        LeaveService $leaveService,
        PeriodService $periodService,
        ReminderService $reminderService,
        TimeRecordService $timeRecordService,
    ) {
        $this->leaveService = $leaveService;
        $this->periodService = $periodService;
        $this->reminderService = $reminderService;
        $this->timeRecordService = $timeRecordService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        if (in_array('upcoming-period', $request->section)) {
            $data['upcoming-period'] = $this->periodService->getUpcomingPeriod($company);
        }
        if (in_array('reminders', $request->section)) {
            $data['reminders'] = $this->reminderService->getReminders($company);
        }
        return $this->sendResponse(new BaseResource($data), 'Dashboard data retrieved successfully.');
    }
}
