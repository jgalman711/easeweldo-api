<?php

namespace App\Services;

use App\Factories\BiometricsStrategyFactory;
use App\Models\Biometrics;
use App\Models\Employee;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BiometricsService
{
    protected $biometrics;

    public function initialize(Biometrics $biometrics)
    {
        $biometrics = BiometricsStrategyFactory::createStrategy($biometrics);
        $biometrics->connect();
        $biometrics->disableDevice();
        return $biometrics;
    }

    public function synchEmployees(Biometrics $biometrics, Collection $employees): void
    {
        throw_if(empty($employees), new Exception("No employees found."));
        $biometrics = self::initialize($biometrics);
        foreach ($employees as $employee) {
            $biometrics->addEmployee($employee);
        }
    }

    public function addEmployee(Biometrics $biometrics, Employee $employee): void
    {
        $biometrics = self::initialize($biometrics);
        $biometrics->addEmployee($employee);
    }

    public function deleteEmployee(Biometrics $biometrics, Employee $employee): void
    {
        $biometrics = self::initialize($biometrics);
        $biometrics->deleteEmployee($employee);
    }

    public function getAttendance(Biometrics $biometrics, Request $request): array
    {
        $startDate = $request->has('start_date') ? new DateTime($request->start_date) : null;
        $endDate = $request->has('end_date') ? new DateTime($request->end_date) : null;
        $biometrics = self::initialize($biometrics);
        $attendance = $this->filterDate($biometrics->getAttendance(), $startDate, $endDate);
        $biometrics->finalize();
        return $attendance;
    }

    private function filterDate(array $attendance, ?DateTime $startDate, ?DateTime $endDate): array
    {
        return array_filter($attendance, function ($item) use ($startDate, $endDate) {
            $timestamp = new DateTime($item['timestamp']);

            if ($startDate && $timestamp >= $startDate && !$endDate) {
                return true;
            }

            if ($endDate && $timestamp <= $endDate && !$startDate) {
                return true;
            }
            return $timestamp >= $startDate && $timestamp <= $endDate;
        });
    }
}
