<?php

namespace App\Factories;

use App\Enumerators\ReportType;
use App\Strategies\Report\AnnualExpenseReportStrategy;

class ReportStrategyFactory
{
    public static function createStrategy(string $type)
    {
        if ($type == ReportType::ANNUAL_EXPENSES) {
            $strategy = new AnnualExpenseReportStrategy();
        }
        return $strategy;
    }
}
