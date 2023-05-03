<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use Carbon\Carbon;
use Exception;

class PayrollService
{
    public function create(array $data): Payroll
    {
        return Payroll::create($data);
    }

    public function compute(Employee $employee, int $periodId): Payroll
    {
        $period = Period::find($periodId);
        if ($period->status != Period::STATUS_PENDING) {
            throw new Exception('Period is already ' . $period->status);
        }

        $workSchedule = $employee->schedules()
            ->where('start_date', '<=', $period->start_date)
            ->first();

        if (!$workSchedule) {
            throw new Exception('No available work schedule for this period');
        }

        $timeRecords = $employee->timeRecords()
            ->whereBetween('created_at', [$period->start_date, $period->end_date])
            ->whereNotNull('expected_clock_in')
            ->whereNotNull('expected_clock_out')
            ->get();

        $totalMinutesLate = 0;
        $totalUnderTime = 0;
        $totalOvertime = 0;
        $absences = 0;
        foreach ($timeRecords as $timeRecord) {
            if ($timeRecord->expected_clock_in && $timeRecord->clock_in == null) {
                $absences++;
                continue;
            }
            $actualClockIn = Carbon::parse(Carbon::parse($timeRecord->clock_in)->format('H:i:s'));
            $expectedClockIn = Carbon::parse($timeRecord->expected_clock_in);

            $actualClockOut = Carbon::parse(Carbon::parse($timeRecord->clock_out)->format('H:i:s'));
            $expectedClockOut = Carbon::parse($timeRecord->expected_clock_out);
            $minutesLate = $actualClockIn->greaterThan($expectedClockIn)
                ? $actualClockIn->diffInMinutes($expectedClockIn)
                : 0;
            $underTime = $expectedClockOut->greaterThan($actualClockOut)
                ? $expectedClockOut->diffInMinutes($actualClockOut)
                : 0;
            $overtime = $actualClockOut->greaterThan($expectedClockOut)
                && ($actualClockOut->diffInMinutes($expectedClockOut) > 60)
                ? $actualClockOut->diffInMinutes($expectedClockOut)
                : 0;

            $totalMinutesLate += $minutesLate;
            $totalUnderTime += $underTime;
            $totalOvertime += $overtime;
        }
        dd($totalMinutesLate, $totalUnderTime, $totalOvertime);
    }
}

