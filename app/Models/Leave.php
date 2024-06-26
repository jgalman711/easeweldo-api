<?php

namespace App\Models;

use App\Enumerators\LeaveEnumerator;
use App\StateMachines\Contracts\LeaveStateContract;
use App\StateMachines\Leave\BaseState;
use App\StateMachines\Leave\SubmittedState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RingleSoft\LaravelProcessApproval\Contracts\ApprovableModel;
use RingleSoft\LaravelProcessApproval\Models\ProcessApproval;
use RingleSoft\LaravelProcessApproval\Traits\Approvable;

class Leave extends Model implements ApprovableModel
{
    use HasFactory, SoftDeletes, Approvable;

    public bool $autoSubmit = true;

    protected $fillable = [
        'company_id',
        'employee_id',
        'created_by',
        'title',
        'type',
        'description',
        'hours',
        'date',
        'submitted_date',
        'remarks',
        'status',
    ];

    public function state(): LeaveStateContract
    {
        return match ($this->status) {
            LeaveEnumerator::SUBMITTED => new SubmittedState($this),
            default => new BaseState($this)
        };
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function onApprovalCompleted(ProcessApproval $approval): bool
    {
        return true;
    }
}
