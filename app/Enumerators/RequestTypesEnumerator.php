<?php

namespace App\Enumerators;

class RequestTypesEnumerator
{
    public const LEAVE = 'leave';
    public const OVERTIME = 'overtime';
    public const TIME_CORRECTION = 'time-correction';

    public const REQUEST_TYPES = [
        self::LEAVE,
        self::OVERTIME,
        self::TIME_CORRECTION
    ];
}
