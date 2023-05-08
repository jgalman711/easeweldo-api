<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\TimeRecord;
use Exception;

class TimeRecordService
{
    public function create(Employee $employee): TimeRecord
    {
        $workSchedule = $employee->schedules()
            ->where('start_date', '<=', now())
            ->first();
        throw_unless($workSchedule, new Exception('No available work schedule'));

        $dayClockInProperty = strtolower(date('l')) . '_clock_in_time';
        $dayClockOutProperty = strtolower(date('l')) . '_clock_out_time';

        $expectedClockIn = $workSchedule->$dayClockInProperty;
        $expectedClockOut =  $workSchedule->$dayClockOutProperty;

        return TimeRecord::create([
            'employee_id' => $employee->id,
            'expected_clock_in' => $expectedClockIn,
            'expected_clock_out' => $expectedClockOut
        ]);
    }
}

