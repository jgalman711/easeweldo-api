<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class TimeRecordService
{
    protected const CLOCK_IN_TIME_SUFFIX = '_clock_in_time';
    protected const CLOCK_OUT_TIME_SUFFIX = '_clock_out_time';

    public function create(
        Employee $employee,
        string $clockInDate = null,
        string $clockOutDate = null
    ): ?TimeRecord {
        list($expectedClockIn, $expectedClockOut) = $this->getExpectedScheduleOf(
            $employee,
            $clockInDate,
            $clockOutDate
        );

        if ($expectedClockIn && $expectedClockOut) {
            $expectedClockIn = Carbon::parse($clockInDate)->format('Y-m-d')
                . ' ' . Carbon::parse($expectedClockIn)->format('H:i:s');
            $expectedClockOut = Carbon::parse($clockOutDate)->format('Y-m-d')
                . ' ' . Carbon::parse($expectedClockOut)->format('H:i:s');

            return TimeRecord::create([
                'employee_id' => $employee->id,
                'expected_clock_in' => $expectedClockIn,
                'expected_clock_out' => $expectedClockOut
            ]);
        }
        return null;
    }

    public function getExpectedScheduleOf(
        Employee $employee,
        string $clockInDate = null,
        string $clockOutDate = null
    ): array {
        $clockInDate = is_null($clockInDate) ? Carbon::now() : Carbon::parse($clockInDate);
        $clockOutDate = is_null($clockOutDate) ? Carbon::now() : Carbon::parse($clockOutDate);

        $workSchedule = $employee->schedules()
            ->wherePivot('start_date', '<=', now())
            ->first();

        throw_unless($workSchedule, new Exception('No available work schedule'));

        $dayClockInProperty = strtolower($clockInDate->dayName) . self::CLOCK_IN_TIME_SUFFIX;
        $dayClockOutProperty = strtolower($clockOutDate->dayName) . self::CLOCK_OUT_TIME_SUFFIX;

        return [
            $workSchedule->$dayClockInProperty,
            $workSchedule->$dayClockOutProperty
        ];
    }

    public function getTimeRecordsByDateRange(Employee $employee, string $dateFrom, string $dateTo): Collection
    {
        return $employee->timeRecords()
            ->where(function ($query) use ($dateFrom, $dateTo) {
                $dateTo = Carbon::parse($dateTo)->addDay();
                $query->whereBetween('expected_clock_in', [$dateFrom, $dateTo])
                    ->orWhereBetween('expected_clock_out', [$dateFrom, $dateTo])
                    ->orWhereBetween('clock_in', [$dateFrom, $dateTo])
                    ->orWhereBetween('clock_out', [$dateFrom, $dateTo]);
            })->get();
    }
}