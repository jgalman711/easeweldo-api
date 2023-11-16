<?php

namespace App\Factories;

use App\Enumerators\ReportEnumerator;
use App\Strategies\Report\ExpensesReportStrategy;
use App\Strategies\Report\MonthlyReportStrategy;
use Exception;

class ReportStrategyFactory
{
    public static function createStrategy(string $type)
    {
        if ($type == ReportEnumerator::EXPENSES) {
            $strategy = new ExpensesReportStrategy;
        } elseif ($type == ReportEnumerator::MONTHLY_SUMMARY) {
            $strategy = new MonthlyReportStrategy;
        } else {
            throw new Exception("Invalid report type");
        }
        return $strategy;
    }
}
