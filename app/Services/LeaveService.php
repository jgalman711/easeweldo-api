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

    public function filter(Request $request, $query)
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

    public function apply(Company $company, Employee $employee, LeaveRequest $leaveRequest): array
    {
        $leaves = [];
        $fromDate = Carbon::parse($leaveRequest->from_date);
        $toDate = Carbon::parse($leaveRequest->to_date);
        $days = $fromDate->diffInDays($toDate) + 1;
        if ($leaveRequest->type !== Leave::TYPE_WITHOUT_PAY) {
            $availableHoursLeaveType = "available_{$leaveRequest->type}_hours";
            $remainingLeaveHours = $employee->salaryComputation->{$availableHoursLeaveType};
            $totalHoursLeave = $leaveRequest->hours * $days;
            throw_if(
                $remainingLeaveHours <= $totalHoursLeave,
                new Exception('No available leaves left for this type.')
            );
        }
        while ($fromDate->lte($toDate)) {
            $leave = Leave::create([
                'company_id' => $company->id,
                'employee_id' => $employee->id,
                'created_by' => $employee->user->id,
                'type' => $leaveRequest->type,
                'description' => $leaveRequest->description,
                'hours' => $leaveRequest->hours,
                'date' => $fromDate,
                'submitted_date' => Carbon::now()->toDateString(),
                'remarks' => $leaveRequest->remarks,
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
