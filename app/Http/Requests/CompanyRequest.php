<?php

namespace App\Http\Requests;

class CompanyRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:companies,name,NULL,id,deleted_at,NULL'
        ];
    }
}
