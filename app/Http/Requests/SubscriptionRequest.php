<?php

namespace App\Http\Requests;

use App\Enumerators\SubscriptionEnumerator;

class SubscriptionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'action' => 'nullable|string|in:' . implode(',', SubscriptionEnumerator::ACTIONS),
            'subscription_id' => 'required|exists:subscriptions,id',
            'months' => ['required', 'in:1,12,24,36'],
            'employee_count' => parent::NULLABLE_NUMERIC,
            'status' => 'nullable|string|in:' . implode(',', SubscriptionEnumerator::STATUSES)
        ];
    }
}
