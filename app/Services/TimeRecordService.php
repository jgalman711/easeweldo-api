<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Exception;

class TimeRecordService
{
    public function create(
        Employee $employee,
        string $clockInDate = null,
        string $clockOutDate = null
    ): TimeRecord {

        $clockInDate = is_null($clockInDate) ? Carbon::now() : Carbon::parse($clockInDate);
        $clockOutDate = is_null($clockOutDate) ? Carbon::now() : Carbon::parse($clockOutDate);

        $workSchedule = $employee->schedules()
            ->where('start_date', '<=', now())
            ->first();
        throw_unless($workSchedule, new Exception('No available work schedule'));

        $dayClockInProperty = strtolower($clockInDate->dayName ) . '_clock_in_time';
        $dayClockOutProperty = strtolower($clockOutDate->dayName ) . '_clock_out_time';

        $expectedClockIn = $workSchedule->$dayClockInProperty;
        $expectedClockOut =  $workSchedule->$dayClockOutProperty;

        return TimeRecord::create([
            'employee_id' => $employee->id,
            'expected_clock_in' => $expectedClockIn,
            'expected_clock_out' => $expectedClockOut
        ]);
    }
}
