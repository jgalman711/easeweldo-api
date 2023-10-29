<?php

namespace App\Http\Requests\Report;

use App\Http\Requests\BaseRequest;

class ExpensesRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'from_date' => self::NULLABLE_DATE,
            'to_date' => self::NULLABLE_DATE,
            'employee_id' => 'exists:employees,id'
        ];
    }
}
