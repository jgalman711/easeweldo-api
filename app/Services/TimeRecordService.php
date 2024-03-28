<?php

namespace App\Services;

use App\Models\Biometrics;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Period;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;

class TimeRecordService
{
    protected const CLOCK_IN_TIME_SUFFIX = '_clock_in_time';

    protected const CLOCK_OUT_TIME_SUFFIX = '_clock_out_time';

    protected const CLOCK_IN = 'clock_in';

    protected const CLOCK_OUT = 'clock_out';

    public function create(
        Employee $employee,
        ?string $clockInDate = null,
        ?string $clockOutDate = null
    ): ?TimeRecord {
        [$expectedClockIn, $expectedClockOut] = $this->getExpectedScheduleOf(
            $employee,
            $clockInDate,
            $clockOutDate
        );

        if ($expectedClockIn && $expectedClockOut) {
            $expectedClockIn = Carbon::parse($clockInDate)->format('Y-m-d')
                .' '.Carbon::parse($expectedClockIn)->format('H:i:s');
            $expectedClockOut = Carbon::parse($clockOutDate)->format('Y-m-d')
                .' '.Carbon::parse($expectedClockOut)->format('H:i:s');

            return TimeRecord::create([
                'employee_id' => $employee->id,
                'expected_clock_in' => $expectedClockIn,
                'expected_clock_out' => $expectedClockOut,
            ]);
        }

        return null;
    }

    public function getExpectedScheduleOf(
        Employee $employee,
        ?string $clockInDate = null,
        ?string $clockOutDate = null
    ): array {
        $clockInDate = is_null($clockInDate) ? Carbon::now() : Carbon::parse($clockInDate);
        $clockOutDate = is_null($clockOutDate) ? Carbon::now() : Carbon::parse($clockOutDate);

        $workSchedule = $employee->schedules()
            ->wherePivot('start_date', '<=', now())
            ->first();

        throw_unless($workSchedule, new Exception('No available work schedule'));

        $dayClockInProperty = strtolower($clockInDate->dayName).self::CLOCK_IN_TIME_SUFFIX;
        $dayClockOutProperty = strtolower($clockOutDate->dayName).self::CLOCK_OUT_TIME_SUFFIX;

        return [
            'expected_clock_in' => $workSchedule->$dayClockInProperty,
            'expected_clock_out' => $workSchedule->$dayClockOutProperty,
        ];
    }

    public function getTimeRecordsByDateRange(
        Relation $timeRecordsQuery,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): Relation {
        return $timeRecordsQuery
            ->where(function ($query) {
                $query->whereNotNull('clock_in')
                    ->orWhereNotNull('clock_out')
                    ->orWhereNotNull('expected_clock_in')
                    ->orWhereNotNull('expected_clock_out');
            })->when($dateFrom, function ($query) use ($dateFrom) {
                $query->where(function ($innerQuery) use ($dateFrom) {
                    $innerQuery->whereDate('expected_clock_in', '>=', $dateFrom)
                        ->orWhereDate('clock_in', '>=', $dateFrom);
                });
            })->when($dateTo, function ($query) use ($dateTo) {
                $query->where(function ($innerQuery) use ($dateTo) {
                    $innerQuery->whereDate('expected_clock_in', '<=', $dateTo)
                        ->orWhereDate('clock_in', '<=', $dateTo);
                });
            });
    }

    public function getTimeRecordToday(Employee $employee): ?TimeRecord
    {
        return $this->getTimeRecordsByDateRange(
            $employee->timeRecords(),
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay(),
        )->first();
    }

    public function setExpectedScheduleOf(Employee $employee, ?Carbon $day = null): TimeRecord
    {
        $day = $day ?? now();
        $expectedSchedule = $this->getExpectedScheduleOf($employee);
        $expectedSchedule['expected_clock_in'] = Carbon::parse($expectedSchedule['expected_clock_in'])
            ->setDate($day->year, $day->month, $day->day);
        $expectedSchedule['expected_clock_out'] = Carbon::parse($expectedSchedule['expected_clock_out'])
            ->setDate($day->year, $day->month, $day->day);

        $timeRecord = TimeRecord::whereDate('clock_in', $expectedSchedule['expected_clock_in'])->first();
        if ($timeRecord) {
            $timeRecord->update($expectedSchedule);

            return $timeRecord;
        }

        return TimeRecord::updateOrCreate($expectedSchedule, [
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
        ]);
    }

    public function setExpectedScheduleByPeriod(Employee $employee, Period $period): void
    {
        $period = CarbonPeriod::create($period->start_date, $period->end_date);
        foreach ($period as $date) {
            $expectedSchedule = $this->getExpectedScheduleOf($employee, $date, $date);
            if (self::isExpected($expectedSchedule)) {
                $this->setExpectedScheduleOf($employee, $date);
            }
        }
    }

    public function synchFromBiometrics(array $attendance, Company $company)
    {
        foreach ($attendance as $record) {
            $employee = $company->employees()->where('company_employee_id', $record['id'])->first();

            if ($record['type'] == Biometrics::TYPE_CLOCK_IN) {
                $clock = self::CLOCK_IN;
                self::timeRecordFirstOrCreate($company, $employee, $record, $clock);
            } elseif ($record['type'] == Biometrics::TYPE_CLOCK_OUT) {
                $clock = self::CLOCK_OUT;
                $timeRecord = TimeRecord::where([
                    'company_id' => $company->id,
                    'employee_id' => $employee->id,
                ])->where(function ($query) use ($clock, $record) {
                    $query->where("original_{$clock}", $record['timestamp'])
                        ->orWhere($clock, $record['timestamp']);
                })->first();

                if (! $timeRecord) {
                    $latestTimeRecord = TimeRecord::where([
                        'company_id' => $company->id,
                        'employee_id' => $employee->id,
                    ])
                        ->whereNull(self::CLOCK_OUT)
                        ->latest('id')
                        ->first();

                    if ($latestTimeRecord && $latestTimeRecord->clock_in && ! $latestTimeRecord->clock_out) {
                        $latestTimeRecord->update([
                            'clock_out' => $record['timestamp'],
                        ]);
                    } else {
                        self::timeRecordFirstOrCreate($company, $employee, $record, $clock);
                    }
                }
            }
        }
    }

    protected function timeRecordFirstOrCreate(
        Company $company,
        Employee $employee,
        array $record,
        string $clock
    ): TimeRecord {
        $timeRecord = TimeRecord::where([
            'company_id' => $company->id,
            'employee_id' => $employee->id,
        ])->where(function ($query) use ($clock, $record) {
            $query->where("original_{$clock}", $record['timestamp'])
                ->orWhere($clock, $record['timestamp']);
        })->first();

        if (! $timeRecord) {
            $timeRecord = TimeRecord::create([
                'company_id' => $company->id,
                'employee_id' => $employee->id,
                $clock => $record['timestamp'],
            ]);
        }

        return $timeRecord;
    }

    private function isExpected(array $expectedSchedule): bool
    {
        return isset(
            $expectedSchedule['expected_clock_in']) &&
            $expectedSchedule['expected_clock_in'] &&
            isset($expectedSchedule['expected_clock_out']) &&
            $expectedSchedule['expected_clock_out'];
    }
}
