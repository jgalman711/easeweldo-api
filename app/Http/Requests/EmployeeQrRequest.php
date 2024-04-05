<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeQrRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'action' => 'required|in:clock',
            'employee_id' => 'required|exists:employees,id',
            'geolocation' => 'string',
        ];
    }
}
