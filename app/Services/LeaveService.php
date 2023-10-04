<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function getLeaveByDateRange(Employee $employee, Carbon $dateFrom = null, Carbon $dateTo = null): Collection
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

    public function filter(Request $request, $query): Collection
    {
        if ($request->has('filter')) {
            foreach ($request->filter as $key => $value) {
                if ($key == 'from_date') {
                    $query->whereDate('date', '>=', $value);
                } elseif ($key == 'to_date') {
                    $query->whereDate('date', '<=', $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }
        if ($request->has('sort')) {
            $sortColumn = $request->input('sort');
            $sortDirection = 'asc';
            if (strpos($sortColumn, '-') === 0) {
                $sortDirection = 'desc';
                $sortColumn = ltrim($sortColumn, '-');
            }
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'asc');
        }
        if ($request->has('per_page')) {
            $perPage = $request->input('per_page', 10);
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function apply(Employee $employee, array $data): array
    {
        $employeeSalaryDetails = $employee->salaryComputation;
        $workHoursPerDay = $employeeSalaryDetails->working_hours_per_day;

        $availableHoursLeaveType = "available_" . $data['type'] . "_leave_hours";
        $remainingLeaveHours = $employee->salaryComputation->{$availableHoursLeaveType};
        $fromDate = Carbon::parse($data['from_date']);
        $toDate = Carbon::parse($data['to_date']);
        $days = $fromDate->diffInDays($toDate);
        $leaves = [];
        if ($days > 0) {
            $currentDate = $fromDate;
            while ($currentDate <= $toDate && $remainingLeaveHours > 0) {
                $leaves[] = $this->createLeave($employee, $data);
                $currentDate->addDay();
            }
        } else {
            $data['date'] = $fromDate;
            $hours = $fromDate->diffInMinutes($toDate) / 60;
            $breakHours = $employeeSalaryDetails->break_hours_per_day;

            $hours = $hours < ($workHoursPerDay / 2) + $breakHours ? $hours : $hours - $breakHours;
            if ($remainingLeaveHours >= $hours) {
                $data['hours'] = $hours;
                $remainingLeaveHours -= $hours;
            } else {
                $data['hours'] = $remainingLeaveHours;
                $remainingLeaveHours = 0;
            }
            $leaves[] = $this->createLeave($employee, $data);
        }
        $employee->salaryComputation->{$availableHoursLeaveType} = $remainingLeaveHours;
        $employee->salaryComputation->save();
        return $leaves;
    }

    public function createLeave(Employee $employee, array $data): Leave
    {
        throw_if($data['hours'] <= 0, new Exception("Insufficient leave balance."));
        return Leave::create([
            'company_id' => $data['company_id'],
            'employee_id' => $employee->id,
            'created_by' => Auth::id(),
            'type' => $data['type'] . '_leave',
            'description' => $data['description'],
            'hours' => $data['hours'],
            'date' => Carbon::parse($data['date'])->toDateString(),
            'submitted_date' => Carbon::now()->toDateString(),
            'remarks' => $data['remarks'] ?? null,
            'status' => Leave::PENDING
        ]);

    }

    public function approve(Leave $leave, string $remarks = null): Leave
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
