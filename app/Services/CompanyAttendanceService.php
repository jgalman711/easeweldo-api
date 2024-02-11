<?php

namespace App\Services;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CompanyAttendanceService
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function getAttendanceSummaryByWeek(Company $company): array
    {
        $startDate = Carbon::now()->subDays(7)->startOfDay();
        $endDate = Carbon::now()->subDays(1)->endOfDay();
        $timeRecords = $company->timeRecords()
            ->whereBetween('expected_clock_in', [$startDate, $endDate])
            ->get();
        return $this->getAttendanceSummary($timeRecords);
    }

    public function getAttendanceSummary(Collection $timeRecords): array
    {
        $groupedRecords = $timeRecords->groupBy(function ($record) {
            return Carbon::parse($record->expected_clock_in)->toDateString();
        });
        $summary = [
            'total_absents' => 0,
            'total_lates' => 0
        ];
        $date = Carbon::now();
        $days = [];
        for ($i = 1; $i <= 7; $i++) {
            $formattedDate = $date->toDateString();
            $days[] = $date->shortEnglishDayOfWeek;
            $absentsPerDay = 0;
            $latesPerDay = 0;
            if (isset($groupedRecords[$formattedDate])) {
                foreach ($groupedRecords[$formattedDate] as $record) {
                    if (!$record->clock_in && !$record->clock_out) {
                        $absentsPerDay++;
                        $summary['total_absents']++;
                    } elseif($this->attendanceService->calculateLates($record->expected_clock_in, $record->clock_in)) {
                        $latesPerDay++;
                        $summary['total_lates']++;
                    }
                }
            }
            $lates[] = $latesPerDay;
            $absents[] = $absentsPerDay;
            $date->subDays(1);
        }
        $summary['week'] = [
            'days' => array_reverse($days),
            'lates' => array_reverse($lates),
            'absents' => array_reverse($absents),
        ];
        $summary['average_absents'] = round($summary['total_absents'] / 7, 2);
        $summary['average_lates'] = round($summary['total_lates'] / 7, 2);
        $summary['average_absents_lates'] = round(
            $summary['average_absents'] +
            $summary['average_lates'] / 2
        , 2);
        return $summary;
    }
}
