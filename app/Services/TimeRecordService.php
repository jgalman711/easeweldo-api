<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Exception;

class TimeRecordService
{
    protected const CLOCK_IN_TIME_SUFFIX = '_clock_in_time';
    protected const CLOCK_OUT_TIME_SUFFIX = '_clock_out_time';

    public function create(
        Employee $employee,
        string $clockInDate = null,
        string $clockOutDate = null
    ): TimeRecord {
        list($expectedClockIn, $expectedClockOut) = $this->getExpectedScheduleOf(
            $employee,
            $clockInDate,
            $clockOutDate
        );
        return TimeRecord::create([
            'employee_id' => $employee->id,
            'expected_clock_in' => $expectedClockIn,
            'expected_clock_out' => $expectedClockOut
        ]);
    }

    public function getExpectedScheduleOf(
        Employee $employee,
        string $clockInDate = null,
        string $clockOutDate = null
    ): array {
        $clockInDate = is_null($clockInDate) ? Carbon::now() : Carbon::parse($clockInDate);
        $clockOutDate = is_null($clockOutDate) ? Carbon::now() : Carbon::parse($clockOutDate);

        $workSchedule = $employee->schedules()
            ->where('start_date', '<=', now())
            ->first();
        throw_unless($workSchedule, new Exception('No available work schedule'));

        $dayClockInProperty = strtolower($clockInDate->dayName) . self::CLOCK_IN_TIME_SUFFIX;
        $dayClockOutProperty = strtolower($clockOutDate->dayName) . self::CLOCK_OUT_TIME_SUFFIX;

        return [
            $workSchedule->$dayClockInProperty,
            $workSchedule->$dayClockOutProperty
        ];
    }
}
