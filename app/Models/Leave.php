<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    protected $fillable = [
        'company_id',
        'employee_id',
        'created_by',
        'type',
        'start_date',
        'end_date',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
