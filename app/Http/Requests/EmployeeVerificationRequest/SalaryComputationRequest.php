<?php

namespace App\Http\Requests\EmployeeVerificationRequest;

use App\Http\Requests\SalaryComputationRequest as BaseSalaryComputationRequest;

class SalaryComputationRequest extends BaseSalaryComputationRequest
{
    public function messages()
    {
        return [
            'required' => 'This field is required.'
        ];
    }
}
