<?php

namespace App\Factories;

use App\Enumerators\ReportEnumerator;
use App\Strategies\Report\AnnualExpenseReportStrategy;
use App\Strategies\Report\MonthlyExpenseReportStrategy;

class ReportStrategyFactory
{
    public static function createStrategy(string $type)
    {
        if ($type == ReportEnumerator::ANNUAL_EXPENSES) {
            $strategy = new AnnualExpenseReportStrategy;
        } elseif ($type == ReportEnumerator::MONTHLY_EXPENSES) {
            $strategy = new MonthlyExpenseReportStrategy;
        }
        return $strategy;
    }
}
