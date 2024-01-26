<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeRecord extends Model
{
    use SoftDeletes;

    public const ATTENDANCE_STATUS = [
        self::ON_TIME,
        self::LATE,
        self::ABSENT,
        self::OVERTIME,
        self::UNDERTIME
    ];

    public const ON_TIME = 'on-time';
    public const LATE = 'late';
    public const ABSENT = 'absent';
    public const OVERTIME = 'overtime';
    public const UNDERTIME = 'undertime';
    public const MISSED_CLOCK_IN = 'missed-clock-in';
    public const MISSED_CLOCK_OUT = 'missed-clock-out';

    protected $fillable = [
        'employee_id',
        'company_id',
        'clock_in',
        'clock_out',
        'expected_clock_in',
        'expected_clock_out',
        'original_clock_in',
        'original_clock_out',
        'source',
        'remarks'
    ];

    protected $appends = [
        'attendance_status',
        'next_action'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeByRange(Builder $timeRecordsQuery, array $range): Builder
    {
        $timeRecordsQuery->where(function ($query) use ($range) {
            $query->when($range['dateFrom'] ?? false, function ($dateFromQuery) use ($range) {
                $dateFromQuery->where('expected_clock_out', '>=', $range['dateFrom']);
            });
            $query->when($range['dateTo'] ?? false, function ($dateToQuery) use ($range) {
                $dateToQuery->where('expected_clock_in', '<=', $range['dateTo']);
            });
        })->orWhere(function ($query) use ($range) {
            $query->when($range['dateFrom'] ?? false, function ($dateFromQuery) use ($range) {
                $dateFromQuery->where('clock_out', '>=', $range['dateFrom']);
            });
            $query->when($range['dateTo'] ?? false, function ($dateToQuery) use ($range) {
                $dateToQuery->where('clock_in', '<=', $range['dateTo']);
            });
        });
        return $timeRecordsQuery;
    }

    public function getAttendanceStatusAttribute()
    {
        $expectedClockIn = $this->parse($this->expected_clock_in);
        $clockIn = $this->parse($this->clock_in);
        $expectedClockOut = $this->parse($this->expected_clock_out);
        $clockOut = $this->parse($this->clock_out);

        if ($this->areDatesSameDay($clockIn, $expectedClockIn) &&
            $this->areDatesSameDay($clockOut, $expectedClockOut)
        ) {
            if ($clockIn->lt($expectedClockIn)) {
                $attendanceStatus = self::ON_TIME;
            } elseif ($clockIn->gt($expectedClockIn)) {
                $attendanceStatus = self::LATE;
            } elseif ($clockOut->lt($expectedClockOut)) {
                $attendanceStatus = self::UNDERTIME;
            } elseif ($clockOut->gt($expectedClockOut)) {
                $attendanceStatus = self::OVERTIME;
            } else {
                $attendanceStatus = self::ON_TIME;
            }
        } elseif (!$clockIn && $clockOut) {
            $attendanceStatus = self::MISSED_CLOCK_IN;
        } elseif ($clockIn && !$clockOut) {
            $attendanceStatus = self::MISSED_CLOCK_OUT;
        } elseif (!$expectedClockIn || $expectedClockOut) {
            $attendanceStatus = self::OVERTIME;
        } else {
            $attendanceStatus = self::ABSENT;
        }
        return $attendanceStatus;
    }

    public function getNextActionAttribute()
    {
        if ($this->clock_in === null && $this->clock_out === null) {
            $action = "Clock In";
        } elseif ($this->clock_in !== null && $this->clock_out === null) {
            $action = "Clock Out";
        } elseif ($this->clock_in !== null && $this->clock_out !== null) {
            $action = "Already Clocked Out";
        } else {
            $action = "Missed Clock In";
        }
        return $action;
    }

    private function areDatesSameDay($clock, $expectedClock): bool
    {
        if ($clock && $expectedClock) {
            return $clock->isSameDay($expectedClock);
        }
        return false;
    }

    private function parse($clock): ?Carbon
    {
        return $clock ? Carbon::parse($clock) : null;
    }
}
