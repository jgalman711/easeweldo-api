<?php

namespace App\Enumerators;

class PayrollEnumerator
{
    public const STATUSES = [
        self::STATUS_TO_PAY,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
        self::STATUS_FAILED,
    ];

    public const STATUS_TO_PAY = 'to-pay';

    public const STATUS_PAID = 'paid';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_FAILED = 'failed';

    public const TYPE = [
        self::TYPE_REGULAR,
        self::TYPE_SPECIAL,
        self::TYPE_NTH_MONTH_PAY,
        self::TYPE_FINAL,
    ];

    public const TYPE_REGULAR = 'regular';

    public const TYPE_SPECIAL = 'special';

    public const TYPE_NTH_MONTH_PAY = 'nth_month_pay';

    public const TYPE_FINAL = 'final';

    public const FORMATS = [
        self::FORMAT_DEFAULT,
        self::FORMAT_DETAILS,
    ];

    public const FORMAT_DEFAULT = 'default';
    public const FORMAT_DETAILS = 'details';

    public const ATTENDANCE_EARNINGS_TYPES = [
        self::ABSENT,
        self::LATE,
        self::UNDERTIME,
        self::OVERTIME,
    ];

    public const ABSENT = 'absent';
    public const LATE = 'late';
    public const UNDERTIME = 'undertime';
    public const OVERTIME = 'overtime';

    public const ERROR_NO_SALARY_DATA = 'No salary data found.';
    public const ERROR_NO_COMPANY_SETTINGS = 'No company settings found.';
}
