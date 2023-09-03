<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPrices extends Model
{
    protected $appends = ['discount'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getDiscountAttribute(): float
    {
        return number_format($this->subscription->amount - $this->price_per_employee, 2);
    }
}
