<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Exception;

class ClockService
{
    public function clockAction(Employee $employee): array
    {
        $currentTime = Carbon::now();
        $currentDate = $currentTime->copy()->format('Y-m-d');

        $timeRecord = $employee->timeRecords()->where(function ($query) use ($currentDate) {
            $query->whereDate('clock_in', $currentDate)
                ->orWhereDate('expected_clock_in', $currentDate);
        })->first();

        if (!$timeRecord) {
            $timeRecord = new TimeRecord();
            $timeRecord->company_id = $employee->company_id;
            $timeRecord->employee_id = $employee->id;
        }

        if ($timeRecord->clock_in == null) {
            $timeRecord->clock_in = $currentTime->toDateTimeString();
            $message = 'Clock in successful.';
        } elseif ($timeRecord->clock_out == null) {
            throw_if(
                $currentTime->diffInMinutes($timeRecord->clock_in) <= 1,
                new Exception("Clock out failed. Please wait for at least 1 minute.")
            );
            $timeRecord->clock_out = $currentTime->toDateTimeString();
            $message = 'Clock out successful.';
        } else {
            throw new Exception("Clock out failed. User already clocked out.");
        }
        $timeRecord->save();
        return [$timeRecord, $message];
    }
}
