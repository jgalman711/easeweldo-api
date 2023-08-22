<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    public const PAID_STATUS = 'paid';

    public const UNPAID_STATUS = 'unpaid';

    public const CORE = 'basic-ease';

    public const TIME_ATTENDANCE = 'time-and-attendance';

    protected $appends = ['discounted_amount'];

    protected $fillable = [
        'name',
        'amount',
        'details'
    ];

    protected $casts = [
        'subscriptions' => 'array'
    ];

    public function getDiscountedAmountAttribute()
    {
        return number_format($this->amount - $this->discount, 2);
    }
}
