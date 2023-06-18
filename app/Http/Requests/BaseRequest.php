<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    protected const REQUIRED_STRING = 'required|string|max:255';
    protected const REQUIRED_DATE = 'required|date';
    protected const REQUIRED_NUMERIC = 'required|numeric|min:0';
    protected const NUMERIC = 'numeric|min:1';
    protected const NULLABLE_NUMERIC = 'nullable|numeric';
    protected const NULLABLE_TIME_FORMAT = 'nullable|date_format:H:i:s';
    protected const NULLABLE_STRING = 'nullable|string|max:255';
    protected const PH_MOBILE_NUMBER = 'regex:/^(09|\+639)\d{9}$/';
    protected const NULLABLE_ARRAY = 'nullable|array|min:1';

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'success' => false,
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }
}
