<?php

namespace App\Http\Requests;

class ReportRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,company_employee_id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after:date_from',
        ];
    }
}
