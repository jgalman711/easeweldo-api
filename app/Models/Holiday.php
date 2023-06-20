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
        self::SPECIAL_HOLIDAY
    ];
    public const REGULAR_HOLIDAY = "Regular Holiday";

    public const SPECIAL_HOLIDAY = "Special Working Day";

    protected $fillable = [
        "name",
        "type",
        "date"
    ];
}
