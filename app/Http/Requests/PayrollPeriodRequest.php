<?php

namespace App\Http\Requests;

class PayrollPeriodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required',
            'start_date' => 'required_if:type,final_pay|date',
            'end_date' => 'required_if:type,final_pay|date|after:start_date',
            'period_id' => 'required_if:type,regular|numeric',
            'employees' => 'required_if:type,final_pay,thirteenth_month_pay|array'
        ];
    }
}
