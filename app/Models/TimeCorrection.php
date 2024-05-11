<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeCorrection extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_PENDING,
        self::STATUS_REJECTED,
    ];

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PENDING = 'pending';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'employee_id',
        'company_id',
        'date',
        'clock_in',
        'clock_out',
        'title',
        'description',
        'remarks',
        'status',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
