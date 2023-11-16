<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class ReportRequest extends BaseRequest
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
