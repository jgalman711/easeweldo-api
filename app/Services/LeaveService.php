<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class LeaveService
{
    protected $timeRecordService;

    private const HOUR_BREAK = 1;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function getLeaves(Company $company, $employee): array
    {
        $leaves = array();
        if ($employee == 'all') {
            $employees = $company->employees;
            foreach ($employees as $employee) {
                $leaves = array_merge($leaves, $employee->leaves->toArray());
            }
        } else {
            $employee = $company->getEmployeeById($employee);
            $leaves = array_merge($leaves, $employee->leaves->toArray());
        }
        return $leaves;
    }

    public function getLeaveByDateRange(Employee $employee, Carbon $dateFrom, Carbon $dateTo): Collection
    {
        return $employee->leaves()
            ->where(function ($query) use ($dateFrom, $dateTo) {
                $dateTo = Carbon::parse($dateTo)->addDay();
                $query->whereBetween('start_date', [$dateFrom, $dateTo])
                    ->orWhereBetween('end_date', [$dateFrom, $dateTo]);
            })->get();
    }

    public function applyLeave(Employee $employee, array $data): Leave
    {
        $data['type'] = Leave::TYPE_EMERGENCY_LEAVE ? Leave::TYPE_VACATION_LEAVE : $data['type'];
        
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $leaveDuration = $startDate->diffInHours($endDate);
        $hoursInOneWorkingDay = $this->getHoursInOneWorkingDay($employee, $data['start_date'], $data['end_date']);
        
        $leave = $leaveDuration > $hoursInOneWorkingDay
            ? $hoursInOneWorkingDay
            : $leaveDuration / $hoursInOneWorkingDay;
        $availableLeaveType = "available_" . $data['type'] . "s";
        
        throw_if(
            $employee->salaryComputation->{$availableLeaveType} <= $leave,
            new Exception('Insufficient leave')
        );
        $employee->salaryComputation->{$availableLeaveType} -= $leave;
        $employee->salaryComputation->save();

        $createdByUser = Auth::user();
        $data['created_by'] = $createdByUser->id;
        $data['status'] = Leave::PENDING;
        $leave = Leave::create($data);

        if ($createdByUser->hasRole('business-admin') || $createdByUser->hasRole('super-admin')) {
            $this->approve($leave);
        }
        return $leave;
    }

    public function approve(Leave $leave): void
    {
        $leave->status = Leave::APPROVED;
        $leave->save();
    }

    public function getSoonestLeaves(int $companyId): Collection
    {
        $leaves = Leave::where('company_id', $companyId)
            ->where('status', Leave::APPROVED)
            ->whereDate('start_date', '>=', now())
            ->whereDate('start_date', '<=', now()->addDays(7))
            ->orderBy('start_date')
            ->get();
        $groupedLeaves = $leaves->groupBy(function ($item) {
            return Carbon::parse($item->start_date)->format('Y-m-d');
        });
        return $groupedLeaves->sortBy(function ($item) {
            return $item;
        });
    }

    private function getHoursInOneWorkingDay(Employee $employee, string $clockInDate, string $clockOutDate): float
    {
        list($expectedClockIn, $expectedClockOut) = $this->timeRecordService->getExpectedScheduleOf(
            $employee,
            $clockInDate,
            $clockOutDate
        );
        $startDate = Carbon::parse($expectedClockIn);
        $endDate = Carbon::parse($expectedClockOut);
        $hoursDifference = $startDate->diffInHours($endDate);
        if ($hoursDifference >= 9) {
            $hoursDifference -= self::HOUR_BREAK;
        }
        return $hoursDifference;
    }
}
