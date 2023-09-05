<?php

namespace App\Http\Requests;

class BiometricsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => self::REQUIRED_STRING,
            'ip_address' => 'required|ip',
            'port' => self::REQUIRED_NUMERIC,
            'provider' => self::REQUIRED_STRING,
            'model' => self::REQUIRED_STRING,
            'product_number' => self::REQUIRED_STRING,
            'status' => self::REQUIRED_STRING
        ];
    }
}
