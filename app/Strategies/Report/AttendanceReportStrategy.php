<?php

namespace App\Strategies\Report;

use App\Interfaces\ReportStrategy;
use App\Models\Company;
use App\Models\TimeRecord;
use Carbon\Carbon;

class AttendanceReportStrategy implements ReportStrategy
{
    public function generate(Company $company, array $data = [])
    {
        $fromDate = $data['from_date'] ?? null;
        $toDate = $data['to_date'] ?? null;
        $employeeId = $data['employee_id'] ?? null;

        $company->load(['employees.timeRecords' => function ($query) use ($fromDate, $toDate, $employeeId) {
            $query->when($fromDate, function ($query) use ($fromDate) {
                return $query->whereDate('expected_clock_in', '>=', $fromDate);
            })->when($toDate, function ($query) use ($toDate) {
                return $query->whereDate('expected_clock_in', '<=', $toDate);
            })->when($employeeId, function ($query) use ($employeeId) {
                return $query->where('employee_id', $employeeId);
            })->get();
        }]);

        $groupedTimeRecords = [];
        foreach ($company->employees as $employee) {
            foreach ($employee->timeRecords as $timeRecord) {
                $key = $this->getGroupKey($timeRecord);
                $groupedTimeRecords[$key][] = $timeRecord;
            }
        }

        $attendanceStatusCounts = [];
        foreach ($groupedTimeRecords as $yearMonth => $timeRecords) {
            $attendanceStatusCounts[$yearMonth] = $this->getAttendanceCounts($timeRecords);
        }
        ksort($attendanceStatusCounts);
        return $attendanceStatusCounts;
    }

    private function getGroupKey(TimeRecord $timeRecord): string
    {
        if ($timeRecord->clock_in) {
            $dateToParse = $timeRecord->clock_in;
        } elseif ($timeRecord->clock_out) {
            $dateToParse = $timeRecord->clock_in;
        } elseif ($timeRecord->expected_clock_in) {
            $dateToParse = $timeRecord->expected_clock_in;
        } elseif ($timeRecord->expected_clock_out) {
            $dateToParse = $timeRecord->expected_clock_out;
        } else {
            return null;
        }
        $year = Carbon::parse($dateToParse)->format('Y');
        $month = Carbon::parse($dateToParse)->format('m');
        return "$year-$month";
    }

    private function getAttendanceCounts(array $timeRecords): array
    {
        $onTimeCount = 0;
        $lateCount = 0;
        $overtimeCount = 0;
        $absentCount = 0;
        $undertime = 0;
        $missedClockIn = 0;
        $missedClockOut = 0;
        $others = 0;

        foreach ($timeRecords as $timeRecord) {
            switch ($timeRecord->attendance_status) {
                case TimeRecord::ON_TIME:
                    $onTimeCount++;
                    break;
                case TimeRecord::LATE:
                    $lateCount++;
                    break;
                case TimeRecord::OVERTIME:
                    $overtimeCount++;
                    break;
                case TimeRecord::ABSENT:
                    $absentCount++;
                    break;
                case TimeRecord::UNDERTIME:
                    $undertime++;
                    break;
                case TimeRecord::MISSED_CLOCK_IN:
                    $missedClockIn++;
                    break;
                case TimeRecord::MISSED_CLOCK_OUT:
                    $missedClockOut++;
                    break;
                default:
                    $others++;
                    break;
            }
        }

        return [
            TimeRecord::ON_TIME => $onTimeCount,
            TimeRecord::LATE => $lateCount,
            TimeRecord::OVERTIME => $overtimeCount,
            TimeRecord::ABSENT => $absentCount,
            TimeRecord::UNDERTIME => $undertime,
            TimeRecord::MISSED_CLOCK_IN =>  $missedClockIn,
            TimeRecord::MISSED_CLOCK_OUT => $missedClockOut,
            'others' => $others
        ];
    }

    private function rearrange(array $attendanceStatusCounts)
    {

    }
}
