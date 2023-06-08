<?php

namespace App\Http\Requests;

class PayrollPeriodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required',
            'period_id' => 'required_if:type,regular|numeric',
            'employees' => 'required_if:type,thirteenth_month_pay|array'
        ];
    }
}
