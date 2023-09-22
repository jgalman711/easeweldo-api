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
        'attendance_status'
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
        $expectedClockIn = Carbon::parse($this->expected_clock_in);
        $clockIn = Carbon::parse($this->clock_in);
        $expectedClockOut = Carbon::parse($this->expected_clock_out);
        $clockOut = Carbon::parse($this->clock_out);

        if ($clockIn->isSameDay($expectedClockIn) && $clockOut->isSameDay($expectedClockOut)) {
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
        } elseif (!$clockIn->isValid() && $clockOut->isValid()) {
            $attendanceStatus = self::MISSED_CLOCK_IN;
        } elseif ($clockIn->isValid() && !$clockOut->isValid()) {
            $attendanceStatus = self::MISSED_CLOCK_OUT;
        } else {
            $attendanceStatus = self::ABSENT;
        }
        return $attendanceStatus;
    }
}
