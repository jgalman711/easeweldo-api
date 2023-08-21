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

    public const CORE = 'Basic Ease 200';

    public const TIME_ATTENDANCE = 'Time and Attendance 49';

    protected $fillable = [
        'name',
        'amount',
        'details'
    ];

    protected $casts = [
        'subscriptions' => 'array'
    ];
}
