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
        $company->load('employees.timeRecords');
        $groupedTimeRecords = [];
        foreach ($company->employees as $employee) {
            foreach ($employee->timeRecords as $timeRecord) {
                $year = Carbon::parse($timeRecord->clock_in)->format('Y');
                $month = Carbon::parse($timeRecord->clock_in)->format('m');
                $key = "$year-$month";
                $groupedTimeRecords[$key][] = $timeRecord;
            }
        }

        $attendanceStatusCounts = [];
        foreach ($groupedTimeRecords as $yearMonth => $timeRecords) {
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

            $attendanceStatusCounts[$yearMonth] = [
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
        return $attendanceStatusCounts;
    }
}
