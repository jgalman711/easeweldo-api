<?php

namespace App\Http\Requests;

class PayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'period_id' => 'required|exists:periods,id',
            'basic_salary' => self::REQUIRED_NUMERIC,
            'total_late_minutes' => self::NULLABLE_NUMERIC,
            'total_late_deductions' => self::NULLABLE_NUMERIC,
            'total_absent_days' => self::NULLABLE_NUMERIC,
            'total_absent_deductions' => self::NULLABLE_NUMERIC,
            'total_overtime_minutes' => self::NULLABLE_NUMERIC,
            'total_overtime_pay' => self::NULLABLE_NUMERIC,
            'total_undertime_minutes' => self::NULLABLE_NUMERIC,
            'total_undertime_deductions' => self::NULLABLE_NUMERIC,
            'total_leave_hours' => self::NULLABLE_NUMERIC,
            'total_leave_compensation' => self::NULLABLE_NUMERIC,
            'sss_contribution' => self::NULLABLE_NUMERIC,
            'philhealth_contribution' => self::NULLABLE_NUMERIC,
            'pagibig_contribution' => self::NULLABLE_NUMERIC,
            'total_contributions' => self::NULLABLE_NUMERIC,
            'taxable_income' => self::NULLABLE_NUMERIC,
            'base_tax' => self::NULLABLE_NUMERIC,
            'compensation_level' => self::NULLABLE_NUMERIC,
            'tax_rate' => self::NULLABLE_NUMERIC,
            'income_tax' => self::NULLABLE_NUMERIC,
            'net_salary' => self::NULLABLE_NUMERIC
        ];
    }
}
