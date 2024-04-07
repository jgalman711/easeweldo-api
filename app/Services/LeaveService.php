<?php

namespace App\Services;

use App\Http\Requests\LeaveRequest;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveService
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function getLeaveByDateRange(Employee $employee, ?Carbon $dateFrom = null, ?Carbon $dateTo = null): Collection
    {
        return $employee->leaves()
            ->where(function ($query) use ($dateFrom, $dateTo) {
                $dateTo = Carbon::parse($dateTo)->addDay();
                if ($dateFrom) {
                    $query->whereDate('date', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->whereDate('date', '<=', $dateTo);
                }
            })->get();
    }

    public function apply(Employee $employee, array $data): array
    {
        $leaves = [];
        $fromDate = Carbon::parse($data['from_date']);
        $toDate = Carbon::parse($data['to_date']);
        $days = $fromDate->diffInDays($toDate) + 1;
        if ($data['type'] !== Leave::TYPE_WITHOUT_PAY) {
            $availableHoursLeaveType = "available_{$data['type']}_hours";
            $remainingLeaveHours = $employee->salaryComputation->{$availableHoursLeaveType};
            $totalHoursLeave = $data['hours'] * $days;
            throw_if(
                $remainingLeaveHours <= $totalHoursLeave,
                new Exception('No available leaves left for this type.')
            );
        }
        while ($fromDate->lte($toDate)) {
            $leave = Leave::create([
                'company_id' => $employee->company->id,
                'employee_id' => $employee->id,
                'created_by' => $employee->user->id,
                'type' => $data['type'],
                'description' => $data['description'],
                'hours' => $data['hours'],
                'date' => $fromDate,
                'submitted_date' => Carbon::now()->toDateString(),
                'remarks' => $data['remarks'] ?? null,
                'status' => Leave::PENDING,
            ]);
            array_push($leaves, $leave);
            $fromDate->addDay();
        }

        return $leaves;
    }

    public function createLeave(Employee $employee, array $data): Leave
    {
        throw_if($data['hours'] <= 0, new Exception('Insufficient leave balance.'));

        return Leave::create([
            'company_id' => $data['company_id'],
            'employee_id' => $employee->id,
            'created_by' => Auth::id(),
            'type' => $data['type'].'_leave',
            'description' => $data['description'],
            'hours' => $data['hours'],
            'date' => Carbon::parse($data['date'])->toDateString(),
            'submitted_date' => Carbon::now()->toDateString(),
            'remarks' => $data['remarks'] ?? null,
            'status' => Leave::PENDING,
        ]);

    }

    public function approve(Leave $leave, ?string $remarks = null): Leave
    {
        $leave->status = Leave::APPROVED;
        $leave->approved_by = Auth::id();
        $leave->approved_date = Carbon::now()->toDateString();
        $leave->remarks = $remarks;
        $leave->save();

        return $leave;
    }

    public function getSoonestLeaves(int $companyId): Collection
    {
        $leaves = Leave::where('company_id', $companyId)
            ->where('status', Leave::APPROVED)
            ->whereDate('date', '>=', now())
            ->whereDate('date', '<=', now()->addDays(7))
            ->orderBy('date')
            ->get();
        $groupedLeaves = $leaves->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m-d');
        });

        return $groupedLeaves->sortBy(function ($item) {
            return $item;
        });
    }
}
