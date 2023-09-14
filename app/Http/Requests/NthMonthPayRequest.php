<?php

namespace App\Http\Requests;

class NthMonthPayRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_NUMERIC
        ];
    }
}
