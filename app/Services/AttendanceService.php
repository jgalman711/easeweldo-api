<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceService
{
    protected $absentMinutes = 0;

    protected $lateMinutes = 0;

    protected $underMinutes = 0;

    protected $overtimeMinutes = 0;

    protected $minutesWorked = 0;

    protected const SIXTY_MINUTES = 60;

    public function calculateAbsences(string $workingHoursPerDay): float
    {
        return $workingHoursPerDay * self::SIXTY_MINUTES;
    }

    public function calculateLates(Carbon $expectedClockIn, ?Carbon $clockIn): float
    {
        return $clockIn && $clockIn->gt($expectedClockIn)
            ? $clockIn->diffInMinutes($expectedClockIn)
            : 0;
    }

    public function calculateUndertimes(Carbon $expectedClockOut, ?Carbon $clockOut): float
    {
        return $clockOut && $clockOut->gt($expectedClockOut)
            ? $clockOut->diffInMinutes($expectedClockOut)
            : 0;
    }

    public function calculateOvertime(Carbon $expectedClockOut, ?Carbon $clockOut): float
    {
        return $clockOut && $clockOut->gt($expectedClockOut)
            ? $clockOut->diffInMinutes($expectedClockOut)
            : 0;
    }

    public function formatHourly(?float $minutes = 0, ?float $hourlyRate = 0): float
    {
        return $minutes / self::SIXTY_MINUTES * $hourlyRate;
    }
}
