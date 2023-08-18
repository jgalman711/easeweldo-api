<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Biometrics extends Model
{
    use SoftDeletes;

    public const PROVIDERS = [
        self::ZKTECO_PROVIDER
    ];

    public const TYPE_CLOCK_IN = 0;

    public const TYPE_CLOCK_OUT = 1;

    public const ZKTECO_PROVIDER = 'ZKTeco';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'company_id',
        'ip_address',
        'port',
        'provider',
        'model',
        'product_number',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
