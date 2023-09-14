<?php

namespace App\Http\Requests;

class SpecialPayrollRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'status' => self::REQUIRED_STRING,
            'description' => self::REQUIRED_STRING,
            'pay_amount' => self::REQUIRED_NUMERIC,
            'hours_worked' => self::NULLABLE_NUMERIC,
            'expected_hours_worked' => self::NULLABLE_NUMERIC,
            'overtime_minutes' => self::NULLABLE_NUMERIC,
            'overtime_pay' => self::NULLABLE_NUMERIC,
            'late_minutes' => self::NULLABLE_NUMERIC,
            'late_deductions' => self::NULLABLE_NUMERIC,
            'absent_minutes' => self::NULLABLE_NUMERIC,
            'absent_deductions' => self::NULLABLE_NUMERIC,
            'undertime_minutes' => self::NULLABLE_NUMERIC,
            'undertime_deductions' => self::NULLABLE_NUMERIC,
            'leaves' => self::NULLABLE_JSON,
            'leaves_pay' => self::NULLABLE_NUMERIC,
            'taxable_earnings' => self::NULLABLE_JSON,
            'non_taxable_earnings' => self::NULLABLE_JSON,
            'holidays' => self::NULLABLE_JSON,
            'sss_contributions' => self::NULLABLE_NUMERIC,
            'philhealth_contributions' => self::NULLABLE_NUMERIC,
            'pagibig_contributions' => self::NULLABLE_NUMERIC,
            'withheld_tax' => self::NULLABLE_NUMERIC,
            'remarks' => self::NULLABLE_NUMERIC,
        ];
    }
}
