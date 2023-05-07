<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhilHealth extends Model
{
    use HasFactory, SoftDeletes;

    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';

    protected $table = 'philhealth';

    protected $fillable = [
        'min_compensation',
        'max_compensation',
        'min_contribution',
        'max_contribution',
        'premium_rate',
        'status'
    ];
}
