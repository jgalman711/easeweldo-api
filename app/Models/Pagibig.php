<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagibig extends Model
{
    use HasFactory, SoftDeletes;

    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    public const MAX_SALARY = 5000;

    protected $table = 'pagibig';

    protected $fillable = [
        'min_compensation',
        'max_compensation',
        'employee_contribution_rate',
        'employer_contribution_rate',
        'status',
    ];
}
