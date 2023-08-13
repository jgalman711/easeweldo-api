<?php

namespace App\Http\Requests;

use App\Models\Earning;
use App\Rules\EarningTypeJsonRule;

class EarningRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'non_taxable' => [new EarningTypeJsonRule()],
            'taxable' => [new EarningTypeJsonRule()],
        ];
    }

    protected function getEarningsSchema()
    {
        return  [
            'type' => ['enum' => Earning::TYPES],
            'name' => ['type' => 'string'],
            'pay' => ['type' => 'number'],
        ];
    }
}
