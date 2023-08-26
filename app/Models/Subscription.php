<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['discounted_amount'];

    protected $fillable = [
        'title',
        'amount',
        'description',
        'features'
    ];

    protected $casts = [
        'subscriptions' => 'array'
    ];

    public function subscriptionPrices(): HasMany
    {
        return $this->hasMany(SubscriptionPrices::class);
    }

    public function getDiscountedAmountAttribute(): float
    {
        return number_format($this->amount - $this->discount, 2);
    }
}
