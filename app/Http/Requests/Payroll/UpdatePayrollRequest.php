<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\BaseRequest;
use App\Rules\AttendanceJsonRule;
use App\Rules\EarningTypeJsonRule;
use App\Rules\LeavesJsonRule;

class UpdatePayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => self::REQUIRED_STRING,
            'status' => self::REQUIRED_STRING,
            'description' => self::NULLABLE_STRING,
            'pay_date' => self::REQUIRED_DATE,
            'basic_salary' => self::REQUIRED_NUMERIC,
            'attendance_earnings' => [new AttendanceJsonRule()],
            'leaves' => [new LeavesJsonRule()],
            'taxable_earnings' => [new EarningTypeJsonRule()],
            'non_taxable_earnings' => [new EarningTypeJsonRule()],
            'holidays' => self::NULLABLE_NUMERIC,
            'holidays_worked' => self::NULLABLE_NUMERIC,
            'sss_contributions' => self::NULLABLE_NUMERIC,
            'philhealth_contributions' => self::NULLABLE_NUMERIC,
            'pagibig_contributions' => self::NULLABLE_NUMERIC,
            'withheld_tax' => self::NULLABLE_NUMERIC,
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
