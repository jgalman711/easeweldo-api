<?php

namespace App\Http\Requests\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Requests\BaseRequest;
use App\Rules\EarningTypeJsonRule;
use App\Rules\RateHourJsonRule;
use App\Rules\RegularEarningsRule;

class UpdatePayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "status" => 'required|in:' . implode(',', PayrollEnumerator::STATUSES),
            "description" => self::NULLABLE_STRING,
            "payDate" => self::REQUIRED_DATE_AFTER_TODAY,
            "basicSalary" => self::REQUIRED_NUMERIC,
            "regularEarnings.overtime" => [new RateHourJsonRule()],
            "regularEarnings.regularHoliday" => [new RateHourJsonRule()],
            "regularEarnings.regularHolidayWorked" => [new RateHourJsonRule()],
            "regularEarnings.specialHoliday" => [new RateHourJsonRule()],
            "regularEarnings.specialHolidayWorked" => [new RateHourJsonRule()],
            "regularEarnings.sickLeave" => [new RateHourJsonRule()],
            "regularEarnings.vacationLeave" => [new RateHourJsonRule()],
            "otherEarnings.taxableEarnings" => [new EarningTypeJsonRule()],
            "otherEarnings.nonTaxableEarnings" => [new EarningTypeJsonRule()],
            "taxesAndContributions.sssContributions" => self::NULLABLE_NUMERIC,
            "taxesAndContributions.philhealthContributions" => self::NULLABLE_NUMERIC,
            "taxesAndContributions.pagibigContributions" => self::NULLABLE_NUMERIC,
            "taxesAndContributions.withheldTax" => self::NULLABLE_NUMERIC,
            "deductions.late" => [new RateHourJsonRule()],
            "deductions.absent" => [new RateHourJsonRule()],
            "deductions.undertime" => [new RateHourJsonRule()],
            "otherDeductions" => [new EarningTypeJsonRule()],
        ];
    }
}
