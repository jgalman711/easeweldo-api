<?php

namespace App\Http\Requests\Qr;

use App\Http\Requests\BaseRequest;

class CompanyQrRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'geolocation' => 'string'
        ];
    }
}
