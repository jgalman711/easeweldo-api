<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'requester_employee_id',
        'request_type',
        'order'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }
}
