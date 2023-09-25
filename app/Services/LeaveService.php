<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
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

    public function applyLeave(Employee $employee, array $data): array
    {
        $dateRange = [];
        $data['type'] = Leave::TYPE_EMERGENCY_LEAVE ? Leave::TYPE_VACATION_LEAVE : $data['type'];
        $startDate = Carbon::parse($data['from_date']);
        $originalEndDate = Carbon::parse($data['to_date']);

        while ($startDate <= $originalEndDate) {
            try {
                DB::beginTransaction();
                $endDate = $startDate->copy()
                ->addHours($employee->salaryComputation->working_hours_per_day)
                ->addHours($employee->salaryComputation->break_hours_per_day);

                $leaveHours = $startDate->diffInHours($endDate);

                if ($leaveHours > $employee->salaryComputation->working_hours_per_day / 2) {
                    $leaveHours -= $employee->salaryComputation->break_hours_per_day;
                }

                $availableHoursLeaveType = "available_" . $data['type'] . "_leave_hours";

                if ($employee->salaryComputation->{$availableHoursLeaveType} < $leaveHours) {
                    $dateRange[] = [
                        'type' => $data['type'],
                        'from_date' => $startDate->toDateTimeString(),
                        'to_date' => $endDate->toDateTimeString(),
                        'status' => 'insufficient balance'
                    ];
                    break;
                } else {
                    $dateRange[] = [
                        'type' => $data['type'],
                        'from_date' => $startDate->toDateTimeString(),
                        'to_date' => $endDate->toDateTimeString()
                    ];
                }

                $employee->salaryComputation->{$availableHoursLeaveType} -= $leaveHours;
                $employee->salaryComputation->save();
                $createdByUser = Auth::user();
                $data['created_by'] = $createdByUser->id;
                $data['status'] = Leave::PENDING;
                $data['type'] .= "_leave";
                $leave = Leave::create($data);
                if ($createdByUser->hasRole('business-admin') || $createdByUser->hasRole('super-admin')) {
                    $this->approve($leave);
                }
                $startDate->addDay();
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        return $dateRange;
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
}
