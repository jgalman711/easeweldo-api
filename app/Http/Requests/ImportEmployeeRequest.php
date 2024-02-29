<?php

namespace App\Http\Requests;

class ImportEmployeeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ];
    }
}
