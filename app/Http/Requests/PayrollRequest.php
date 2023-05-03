<?php

namespace App\Http\Requests;

class PayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'period_id' => 'required|exists:periods,id'
        ];
    }
}
