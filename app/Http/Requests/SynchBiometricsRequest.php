<?php

namespace App\Http\Requests;

class SynchBiometricsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
        ];
    }
}
