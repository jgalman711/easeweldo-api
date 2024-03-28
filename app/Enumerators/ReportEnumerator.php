<?php

namespace App\Enumerators;

class ReportEnumerator
{
    public const TYPE = [
        self::EXPENSES,
        self::MONTHLY_SUMMARY,
    ];

    public const EXPENSES = 'expenses';

    public const MONTHLY_SUMMARY = 'monthly-summary';
}
