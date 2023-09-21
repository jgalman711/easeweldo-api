<?php

namespace App\Http\Requests;

class ReportRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,company_employee_id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after:date_from',
        ];
    }
}
