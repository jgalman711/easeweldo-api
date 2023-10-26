<?php

namespace App\Models;

use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';

    public const STATUSES = [
        self::ACTIVE,
        self::INACTIVE
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email_address',
        'username',
        'status',
        'password',
        'temporary_password',
        'temporary_password_expires_at'
    ];

    protected $appends = ['email'];

    protected $hidden = [
        'password',
        'temporary_password',
        'temporary_password_expires_at',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'temporary_password_expires_at' => 'datetime'
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_users', 'user_id', 'company_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function getEmailAttribute(): ?string
    {
        return $this->email_address;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($this, $token));
    }
}
