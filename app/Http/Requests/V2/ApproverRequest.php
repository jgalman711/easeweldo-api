<?php

namespace App\Http\Requests\V2;

use App\Enumerators\RequestTypesEnumerator;
use App\Http\Requests\BaseRequest;

class ApproverRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'request_type' => 'required|in:'.implode(',', RequestTypesEnumerator::REQUEST_TYPES),
            'order' => self::NULLABLE_NUMERIC
        ];
    }
}
