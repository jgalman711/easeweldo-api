<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'branch',
        'account_name',
        'account_number',
        'email',
        'contact_name',
        'contact_number'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
