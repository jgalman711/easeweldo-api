<?php

namespace App\Services;

use App\Enumerators\PayrollEnumerator;
use App\Models\Company;
use App\Models\Period;
use App\Models\Setting;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PeriodService
{
    private const PAYROLL_ALLOWANCE_DAY = 5;

    private const SEMI_MONTHLY_DAYS = 15;

    private const MINIMUM_LAST_DAY = 28;

    private const LATEST = 'latest';

    protected $today;

    protected $currentMonth;

    protected $currentYear;

    protected $salaryDate;

    public function __construct()
    {
        $this->today = new DateTime();
        $this->currentMonth = $this->today->format('n');
        $this->currentYear = $this->today->format('Y');
        $this->salaryDate = null;
    }

    public function updateStatus(Period $period, string $action): Collection
    {
        DB::transaction(function () use ($period, $action) {
            switch ($action) {
                case 'pay':
                    $payrollStatus = PayrollEnumerator::STATUS_PAID;
                    $periodStatus = Period::STATUS_COMPLETED;
                    break;
                case 'cancel':
                    $payrollStatus = PayrollEnumerator::STATUS_CANCELLED;
                    $periodStatus = Period::STATUS_CANCELLED;
                    break;
                default:
                    $payrollStatus = PayrollEnumerator::STATUS_TO_PAY;
                    $periodStatus = Period::STATUS_PENDING;
                    break;
            }
            $payrolls = $period->payrolls()->where('status', PayrollEnumerator::STATUS_TO_PAY)->get();
            foreach ($payrolls as $payroll) {
                $payroll->status = $payrollStatus;
                $payroll->save();
            }
            $period->status = $periodStatus;
            $period->save();
        });

        return $period->payrolls;
    }

    public function initializeFromSalaryDate(Company $company, DateTime $salaryDate, string $periodCycle): Period
    {
        $salaryDate = Carbon::parse($salaryDate);
        $data['type'] = Period::TYPE_REGULAR;
        $data['subtype'] = $periodCycle;
        $data['salary_date'] = $salaryDate;
        $data['end_date'] = $salaryDate->copy()->subDays(self::PAYROLL_ALLOWANCE_DAY);

        if ($salaryDate->day > self::MINIMUM_LAST_DAY || $data['end_date']->day > self::MINIMUM_LAST_DAY) {
            $data['end_date'] = Carbon::now()->setDay(25);
        }
        if ($periodCycle == Period::SUBTYPE_MONTHLY) {
            $data['start_date'] = $data['end_date']->copy()->subMonth()->addDay();
        } elseif ($periodCycle == Period::SUBTYPE_SEMI_MONTHLY) {
            $data['start_date'] = $data['end_date']->copy()->subDays(self::SEMI_MONTHLY_DAYS)->addDay();
        } elseif ($periodCycle == Period::SUBTYPE_WEEKLY) {
            $data['end_date'] = $salaryDate->copy()->subDays(7);
            $data['start_date'] = $data['end_date']->copy()->subDays(6);
        }
        $companyPreviousPeriod = $company->periods()->latest()->first();
        if ($companyPreviousPeriod && $data['start_date'] <= $companyPreviousPeriod->end_date) {
            $data['start_date'] = $companyPreviousPeriod->end_date->addDay();
        }

        $data['status'] = Period::STATUS_PENDING;
        $data['company_id'] = $company->id;
        $data['company_period_id'] = $companyPreviousPeriod
            ? $companyPreviousPeriod->company_period_id + 1
            : 1;

        return Period::create($data);
    }

    public function initializeFromPreviousPeriod(Company $company): ?Period
    {
        $currentDate = Carbon::now()->toDateString();
        $companyPreviousPeriod = $company->periods()->where('type', Period::TYPE_REGULAR)->latest()->first();
        if ($companyPreviousPeriod) {
            $salaryDate = Carbon::parse($companyPreviousPeriod->salary_date);
        } else {
            $settings = Setting::where('company_id', $company->id)->first();
            throw_unless($settings, new Exception("No settings found for {$company->name}"));
            $salaryDate = $this->convertSalaryDayToDate($settings->salary_day, $settings->period_cycle);

            return $this->initializeFromSalaryDate($company, $salaryDate, $settings->period_cycle);
        }
        if ($companyPreviousPeriod && $salaryDate->copy()->subDays(2) <= $currentDate) {
            $data['company_id'] = $company->id;
            $data['company_period_id'] = $companyPreviousPeriod->company_period_id + 1;
            $data['type'] = $companyPreviousPeriod->type;
            $data['subtype'] = $companyPreviousPeriod->subtype;
            $data['status'] = Period::STATUS_PENDING;
            if ($data['subtype'] == Period::SUBTYPE_SEMI_MONTHLY) {
                $data['start_date'] = Carbon::parse($companyPreviousPeriod->end_date)->addDay();
                $data['end_date'] = Carbon::parse($companyPreviousPeriod->start_date)->addMonth()->subDay();
                $data['salary_date'] = $data['end_date']->copy()->addDays(self::PAYROLL_ALLOWANCE_DAY);
            } elseif ($data['subtype'] == Period::SUBTYPE_MONTHLY) {
                $data['start_date'] = $companyPreviousPeriod->start_date->addMonth();
                $data['end_date'] = $companyPreviousPeriod->end_date->addMonth();
                $data['salary_date'] = $companyPreviousPeriod->salary_date->addMonth();
            } elseif ($data['subtype'] == Period::SUBTYPE_WEEKLY) {
                $data['start_date'] = $companyPreviousPeriod->start_date->addDays(7);
                $data['end_date'] = $companyPreviousPeriod->end_date->addDays(7);
                $data['salary_date'] = $companyPreviousPeriod->salary_date->addDays(7);
            } else {
                throw new Exception('Invalid period type '.$data['type']);
            }

            return Period::create($data);
        }

        return null;
    }

    public function getUpcomingPeriod(Company $company): array
    {
        $now = Carbon::now();
        $targetDate = $now->copy()->addDays(2);
        $period = $company->periods()->whereDate('salary_date', '<=', $targetDate->toDateString())->first();
        $payrolls = $company->payrolls->where('period_id', $period->id);

        return [
            'period' => $period,
            'employees_net_salary' => $payrolls->sum('net_salary'),
            'number_of_employees' => $payrolls->count(),
            'days_before_salary' => $period->salary_date->diffInDays($now),
            'salary_date' => $period->salary_date,
        ];
    }

    public function convertSalaryDayToDate(string|array|int $salaryDay, string $periodCycle): ?DateTime
    {
        if ($periodCycle == Period::SUBTYPE_MONTHLY) {
            $this->salaryDate = $this->salaryDayMonthly($salaryDay);
        } elseif ($periodCycle == Period::SUBTYPE_SEMI_MONTHLY && is_array($salaryDay)) {
            $this->salaryDate = $this->salaryDaySemiMonthly($salaryDay);
        } elseif ($periodCycle == Period::SUBTYPE_WEEKLY) {
            $this->salaryDate = $this->salaryWeekly($salaryDay);
        }
        if ($this->salaryDate) {
            return $this->salaryDate;
        }

        return null;
    }

    public function getCompanyPeriod(Company $company, int|string $periodId): Period
    {
        if ($periodId == self::LATEST) {
            return $company->periods()
                ->whereNotNull('company_period_id')
                ->latest()
                ->firstOrFail();
        } else {
            return $company->periods()->findOrFail($periodId);
        }
    }

    public function getLatestPeriod(Company $company, ?string $type = null): Period
    {
        $periodBuilder = $company->periods()->where('type', $type);

        return $periodBuilder->orderBy('id', 'desc')->first();
    }

    /**
     * TODO: Move this to repository
     */
    public function getCurrentPeriod(Company $company): ?Period
    {
        $periodBuilder = $company->periods()->where('type', Period::TYPE_REGULAR);
        $currentDate = now();
        $periodBuilder->where('start_date', '<=', $currentDate)
            ->where('salary_date', '>=', $currentDate);

        return $periodBuilder->orderBy('id', 'desc')->first();
    }

    public function generateDashboardDetails(Company $company): array
    {
        $period = $this->getCurrentPeriod($company);
        if ($period) {
            $formattedStartDate = Carbon::parse($period->start_date)->isoFormat('MMM D');
            $formattedEndDate = Carbon::parse($period->end_date)->isoFormat('MMM D');
            $previousPeriod = Period::where('company_period_id', $period->company_period_id - 1)->first();
            if ($previousPeriod && $previousPeriod->payrollCost > 0) {
                $diff = round(
                    ($period->payrollCost - $previousPeriod->payrollCost) / $previousPeriod->payrollCost * 100, 2
                );
            }
    
            return [
                'payroll_cost' => number_format($period->payrollCost, 2),
                'pay_date' => Carbon::parse($period->salary_date)->isoFormat('MMM D'),
                'period' => "Period $period->company_period_id: $formattedStartDate - $formattedEndDate",
                'status' => $period->status,
                'difference' => $diff ?? 0,
            ];
        }
        return null;
    }

    private function salaryDayMonthly(int $salaryDay): DateTime
    {
        $salaryDay = $salaryDay - self::PAYROLL_ALLOWANCE_DAY;
        if ($this->today->format('j') > $salaryDay) {
            $this->currentMonth += 1;
            if ($this->currentMonth > 12) {
                $this->currentMonth = 1;
                $this->currentYear += 1;
            }
        }

        return new DateTime($this->currentYear.'-'.$this->currentMonth.'-'.$salaryDay);
    }

    private function salaryDaySemiMonthly(array $salaryDay): DateTime
    {
        foreach ($salaryDay as $day) {
            if ($this->today->format('j') <= $day - self::PAYROLL_ALLOWANCE_DAY) {
                $salaryDate = new DateTime($this->currentYear.'-'.$this->currentMonth.'-'.$day);
                break;
            }
        }

        if (! isset($salaryDate)) {
            $this->currentMonth += 1;
            if ($this->currentMonth > 12) {
                $this->currentMonth = 1;
                $this->currentYear += 1;
            }
            $nextMonth = new DateTime($this->currentYear.'-'.$this->currentMonth.'-1');
            $salaryDay = reset($salaryDay);
            $salaryDate = new DateTime($nextMonth->format('Y-m').'-'.$salaryDay);
        }

        return $salaryDate;
    }

    private function salaryWeekly(string $salaryDay): DateTime
    {
        $salaryDay = strtolower($salaryDay);
        throw_unless(in_array($salaryDay, Period::ALLOWED_DAYS),
            new Exception('Invalid day.')
        );
        $todayDayOfWeek = $this->today->format('N');
        $dayIndex = array_search($salaryDay, Period::ALLOWED_DAYS);
        $daysToAdd = ($dayIndex - $todayDayOfWeek + 8) % 7;
        if ($daysToAdd < 7) {
            $daysToAdd += 7;
        }
        $salaryDate = clone $this->today;
        $salaryDate->modify('+'.$daysToAdd.' day');

        return $salaryDate;
    }
}
