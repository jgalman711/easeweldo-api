<?php

namespace App\Http\Requests\EmployeeVerificationRequest;

use App\Http\Requests\SalaryComputationRequest as BaseSalaryComputationRequest;
use App\Rules\EarningsValidationRule;

class SalaryComputationRequest extends BaseSalaryComputationRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['taxable_earnings'] = [new EarningsValidationRule()];
        $rules['non_taxable_earnings'] = [new EarningsValidationRule()];
        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'This field is required.'
        ];
    }
}
