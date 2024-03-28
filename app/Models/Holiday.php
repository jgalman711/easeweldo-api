<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory, SoftDeletes;

    public const HOLIDAY_TYPES = [
        self::REGULAR_HOLIDAY,
        self::SPECIAL_HOLIDAY,
    ];

    public const REGULAR_HOLIDAY = 'regular';

    public const SPECIAL_HOLIDAY = 'special';

    protected $fillable = [
        'name',
        'type',
        'date',
    ];

    public function getSimplifiedTypeAttribute()
    {
        switch ($this->attributes['type']) {
            case 'Regular Holiday':
                return 'regular';
            case 'Special Non-working Holiday':
                return 'special';
            default:
                return 'unknown';
        }
    }
}
