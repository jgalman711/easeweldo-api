<?php

namespace App\Http\Requests;

use App\Enumerators\DisbursementEnumerator;

class DisbursementRequest extends BaseRequest
{
    public function rules(): array
    {
        $type = $this->input('type');
        $rules = [
            'type' => 'required|in:' . implode(',', DisbursementEnumerator::SPECIAL_TYPES),
            'subtype' => self::NULLABLE_STRING,
            'employee_id' => self::NULLABLE_ARRAY,
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_STRING,
        ];
        if ($type == DisbursementEnumerator::TYPE_NTH_MONTH_PAY) {
            $rules = [
                ...$rules,
                'salary_date' => 'required|date|after_or_equal:today'
            ];
        } elseif ($type == DisbursementEnumerator::TYPE_SPECIAL) {
            $rules = [
                ...$rules,
                'salary_date' => 'required|date|after_or_equal:today',
                'pay_amount' => self::REQUIRED_NUMERIC
            ];
        }
        return $rules;
    }
}
