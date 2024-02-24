<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\BaseRequest;
use App\Rules\DeductionJsonRule;
use App\Rules\EarningTypeJsonRule;

class UpdateRegularPayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "basic_salary" => self::NULLABLE_NUMERIC,
            "overtime" => [new DeductionJsonRule()],
            "regular_holiday" => [new DeductionJsonRule()],
            "regular_holiday_worked" => [new DeductionJsonRule()],
            "special_holiday" => [new DeductionJsonRule()],
            "special_holiday_worked" => [new DeductionJsonRule()],
            "sick_leave" => [new DeductionJsonRule()],
            "vacation_leave" => [new DeductionJsonRule()],
            "taxable_earnings" => [new EarningTypeJsonRule()],
            "non_taxable_earnings" => [new EarningTypeJsonRule()],
            "other_deductions" => [new EarningTypeJsonRule()],
            "sss_contributions" => self::NULLABLE_NUMERIC,
            "philhealth_contributions" => self::NULLABLE_NUMERIC,
            "pagibig_contributions" => self::NULLABLE_NUMERIC,
            "withheld_tax" => self::NULLABLE_NUMERIC,
            "absents" => [new DeductionJsonRule()],
            "lates" => [new DeductionJsonRule()],
            "undertimes" => [new DeductionJsonRule()],
        ];
    }
}
