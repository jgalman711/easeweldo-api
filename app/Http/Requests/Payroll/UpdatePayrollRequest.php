<?php

namespace App\Http\Requests\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Requests\BaseRequest;
use App\Rules\DeductionJsonRule;
use App\Rules\EarningTypeJsonRule;

class UpdatePayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return $this->payroll->type == PayrollEnumerator::TYPE_REGULAR
            ? $this->regularPayrollRules()
            : $this->specialPayrollRules();
    }

    public function regularPayrollRules(): array
    {
        return [
            "basic_salary" => self::NULLABLE_NUMERIC,
            "description" => self::NULLABLE_STRING,
            "overtime" => [new DeductionJsonRule()],
            "regular_holidays" => [new DeductionJsonRule()],
            "regular_holidays_worked" => [new DeductionJsonRule()],
            "special_holidays" => [new DeductionJsonRule()],
            "special_holidays_worked" => [new DeductionJsonRule()],
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

    public function specialPayrollRules(): array
    {
        return [
            "basic_salary" => self::NULLABLE_NUMERIC,
            "description" => self::NULLABLE_STRING,
            "pay_date" => 'required|date|after_or_equal:today',
            'remarks' => self::NULLABLE_STRING
        ];

    }
}
