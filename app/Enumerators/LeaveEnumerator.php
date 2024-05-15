<?php

namespace App\Enumerators;

class LeaveEnumerator
{
    public const STATUSES = [
        self::SUBMITTED,
        self::APPROVED,
        self::DECLINED,
        self::DISCARDED
    ];

    public const SUBMITTED = 'submitted';
    public const APPROVED = 'approved';
    public const DECLINED = 'declined';
    public const DISCARDED = 'discarded';

    public const TYPES = [
        self::TYPE_SICK_LEAVE,
        self::TYPE_VACATION_LEAVE,
        self::TYPE_EMERGENCY_LEAVE,
        self::TYPE_WITHOUT_PAY
    ];

    public const TYPE_SICK_LEAVE = 'sick_leave';
    public const TYPE_VACATION_LEAVE = 'vacation_leave';
    public const TYPE_EMERGENCY_LEAVE = 'emergency_leave';
    public const TYPE_WITHOUT_PAY = 'leave_without_pay';
}
