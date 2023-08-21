<?php

namespace App\Http\Requests;

class SubscriptionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'subscriptions' => ['required', 'array'],
            'subscriptions.*' => ['exists:subscriptions,id'],
            'months' => ['required', 'in:1,3,6,12,24,36'],
        ];
    }
}
