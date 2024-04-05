<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RegistrationResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'token' => $this['token'],
            'user' => new UserResource($this['user']),
            'company' => new CompanyResource($this['company']),
        ];
    }
}
