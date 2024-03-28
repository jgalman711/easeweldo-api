<?php

namespace App\Enumerators;

class AttendanceEarningsEnumerator
{
    public const ABSENT = 'absent';

    public const LATE = 'late';

    public const UNDERTIME = 'undertime';

    public const OVERTIME = 'overtime';

    public const DEDUCTION_TYPES = [
        self::ABSENT,
        self::LATE,
        self::UNDERTIME,
    ];
}
