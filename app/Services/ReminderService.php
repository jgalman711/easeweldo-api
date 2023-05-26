<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Holiday;
use App\Models\Leave;
use Illuminate\Support\Collection;

class ReminderService
{
    private const UPCOMING_HOLIDAY_TITLE = 'UPCOMING HOLIDAY';

    private const EASEWELDO_DUE_TITLE = 'EASEWELDO DUE';

    private const REMINDER_COLOR_DANGER = "#E55857";

    private const REMINDER_COLOR_WARNING = "#F9B425";

    private const REMINDER_COLOR_ACTIVE = "#7ED957";

    private const REMINDER_COLOR_PRIMARY = "#5BA5F4";

    private const REMINDER_COLOR_NEUTRAL = "#A6A6A6";

    public function getReminders(Company $company): Collection
    {
        if ($company->subscription_status == 'unpaid'
            && $company->due_date >= date('Y-m-d')
        ) {
            $companyColor = self::REMINDER_COLOR_DANGER;
        } elseif (
            $company->subscription_status == 'unpaid'
            && $company->due_date >= date('Y-m-d', strtotime('-3 days'))
        ) {
            $companyColor = self::REMINDER_COLOR_WARNING;
        } elseif ($company->subscription_status == 'paid') {
            $companyColor = self::REMINDER_COLOR_ACTIVE;
        } else {
            $companyColor = self::REMINDER_COLOR_NEUTRAL;
        }

        $holidays = Holiday::whereDate('date', '>=', now())
            ->whereDate('date', '<=', now()->addDays(27))
            ->orderBy('date')
            ->get();

        $leaves = Leave::where('company_id', $company->id)
            ->where('status', Leave::APPROVED)
            ->whereDate('start_date', '>=', now())
            ->whereDate('start_date', '<=', now()->addDays(7))
            ->orderBy('start_date')
            ->get();

        return $leaves->map(function ($leave) {
            return [
                'title' => $leave->employee->fullName,
                'date' => $leave->start_date,
                'sub_title' => $leave->type,
                'color' => self::REMINDER_COLOR_NEUTRAL
            ];
        })->concat($holidays->map(function ($holiday) {
            return [
                'title' => self::UPCOMING_HOLIDAY_TITLE,
                'date' => $holiday->date,
                'sub_title' => $holiday->name,
                'color' => self::REMINDER_COLOR_NEUTRAL
            ];
        }))->push([
            'title' => self::EASEWELDO_DUE_TITLE,
            'date' => $company->due_date,
            'sub_title' => $company->amount_due,
            'color' => $companyColor
        ])->sortBy(function ($reminder) {
            return $reminder['date'];
        });
    }
}

