<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function dashboard(Company $company): JsonResponse
    {
        $lates = 0;
        $absents = 0;
        foreach ($company->employees as $employee) {
            $timeRecord = $employee->timeRecords()->whereDate('created_at', date('Y-m-d'))->first();
            if ($timeRecord->clock_in && $timeRecord->clock_in > $timeRecord->expected_clock_in) {
                $lates++;
            } elseif ($timeRecord->clock_in == null) {
                $absents++;
            }
        }
        $dashboardData = [
            "lates" => $lates,
            "absents" => $absents
        ];
        return $this->sendResponse($dashboardData, 'Dashboard data retrieved successfully.');
    }
}
