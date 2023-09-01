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
        'original_clock_in',
        'original_clock_out',
        'source',
        'remarks'
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
}
