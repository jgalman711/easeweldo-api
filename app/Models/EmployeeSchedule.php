<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'work_schedule_id',
        'start_date',
        'status',
        'remarks',
    ];

    public const STATUS_TYPES = [
        self::TYPE_ACTIVE,
        self::TYPE_INACTIVE,
        self::TYPE_UPCOMING,
    ];

    public const TYPE_ACTIVE = 'active';

    public const TYPE_INACTIVE = 'inactive';

    public const TYPE_UPCOMING = 'upcoming';

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
