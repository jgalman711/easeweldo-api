<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Earning extends Model
{
    use SoftDeletes;

    public const CATEGORY_SUFFIX_EARNINGS = '_earnings';

    public const CATEGORIES = [
        self::TAXABLE,
        self::NON_TAXABLE
    ];

    public const TAXABLE = 'taxable';
    public const NON_TAXABLE = 'non_taxable';

    public const TYPES = [
        self::ALLOWANCE,
        self::COMPENSATION,
        self::COMMISSION
    ];

    public const ALLOWANCE = 'allowance';
    public const COMPENSATION = 'compensation';
    public const COMMISSION = 'commission';

    protected $casts = [
        self::TAXABLE => 'array',
        self::NON_TAXABLE => 'array'
    ];

    protected $fillable = [
        'company_id',
        'taxable',
        'non_taxable'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
