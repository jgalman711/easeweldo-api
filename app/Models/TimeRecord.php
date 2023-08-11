<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'company_id',
        'clock_in',
        'clock_out',
        'expected_clock_in',
        'expected_clock_out',
        'attendance_status',
        'remarks'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeByRange(Builder $timeRecordsQuery, array $range): Builder
    {
        if (isset($range['dateTo']) && $range['dateTo']) {
            $timeRecordsQuery->where(function ($query) use ($range) {
                $query->whereDate('expected_clock_in', '>=', $range['dateTo'])
                    ->orWhereDate('clock_in', '>=', $range['dateTo']);
            });
        }

        if (isset($range['dateFrom']) && $range['dateFrom']) {
            $timeRecordsQuery->where(function ($query) use ($range) {
                $query->whereDate('expected_clock_out', '<=', $range['dateFrom'])
                    ->orWhereDate('clock_out', '<=', $range['dateFrom']);
            });
        }

        return $timeRecordsQuery;
    }
}
