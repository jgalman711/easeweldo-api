<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Biometrics extends Model
{
    use SoftDeletes;

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
