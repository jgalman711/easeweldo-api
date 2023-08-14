<?php

namespace App\Services;

use App\Factories\BiometricsStrategyFactory;
use App\Models\Biometrics;
use App\Models\Company;
use App\Models\Employee;
use Exception;
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

    public function addEmployee(Biometrics $biometrics, Employee $employee)
    {
        $biometrics = self::initialize($biometrics);
        $biometrics->addEmployee($employee);
    }

    public function deleteEmployee(Biometrics $biometrics, Employee $employee)
    {
        $biometrics = self::initialize($biometrics);
        $biometrics->deleteEmployee($employee);
    }
}
