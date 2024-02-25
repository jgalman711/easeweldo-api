<?php

namespace App\Http\Requests;

use App\Enumerators\DisbursementEnumerator;

class DisbursementRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|in:' . implode(',', DisbursementEnumerator::SPECIAL_TYPES),
            'subtype' => self::NULLABLE_STRING,
            'employee_id' => self::NULLABLE_ARRAY,
            'description' => self::REQUIRED_STRING,
            'pay_amount' => self::REQUIRED_NUMERIC,
            'start_date' => self::NULLABLE_DATE,
            'end_date' => self::NULLABLE_DATE,
            'salary_date' => 'required|date|after_or_equal:today',
            'remarks' => self::NULLABLE_STRING,
        ];
    }
}
