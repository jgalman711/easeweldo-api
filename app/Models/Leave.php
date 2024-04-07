<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public const TYPE_SICK_LEAVE = 'sick_leave';

    public const TYPE_VACATION_LEAVE = 'vacation_leave';

    public const TYPE_EMERGENCY_LEAVE = 'emergency_leave';

    public const TYPE_WITHOUT_PAY = 'leave_without_pay';

    public const TYPE = 'type';

    public const HOURS = 'hours';

    public const ATTRIBUTES = [
        self::TYPE,
        self::HOURS,
    ];

    public const TYPES = [
        self::TYPE_SICK_LEAVE,
        self::TYPE_VACATION_LEAVE,
        self::TYPE_EMERGENCY_LEAVE,
        self::TYPE_WITHOUT_PAY,
    ];

    protected $fillable = [
        'company_id',
        'employee_id',
        'created_by',
        'title',
        'type',
        'description',
        'hours',
        'date',
        'submitted_date',
        'remarks',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
