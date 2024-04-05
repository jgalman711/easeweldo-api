<?php

namespace App\Strategies\Biometrics;

use App\Helpers\ZKLibrary;
use App\Interfaces\BiometricsStrategy;
use App\Models\Biometrics;
use App\Models\Employee;
use Exception;

class ZKTecoBiometricsStrategy implements BiometricsStrategy
{
    protected $zkTeco;

    protected const RETRIES = 3;

    public function __construct(Biometrics $biometrics)
    {
        $this->zkTeco = new ZKLibrary($biometrics->ip_address, $biometrics->port, 'TCP');
        $this->zkTeco->setTimeout(10);
    }

    public function connect(): void
    {
        $retry = 1;
        do {
            try {
                if (! $this->zkTeco->connect()) {
                    $retry++;

                    continue;
                }

                return;
            } catch (Exception) {
                $retry++;
            }
        } while ($retry <= self::RETRIES);
        throw new Exception(
            "Please ensure your device is connected to the network and try again. Check the device's IP configuration if needed."
        );
    }

    public function disconnect(): void
    {
        $this->zkTeco->disconnect();
    }

    public function enableDevice(): void
    {
        $this->zkTeco->enableDevice();
    }

    public function disableDevice(): void
    {
        $this->zkTeco->disableDevice();
    }

    public function addEmployee(Employee $employee): void
    {
        $this->zkTeco->setUser(
            $employee->company_employee_id,
            $employee->company_employee_id,
            $employee->full_name,
            '',
            0
        );
    }

    public function deleteEmployee(Employee $employee): void
    {
        $this->zkTeco->deleteUser($employee->id);
    }

    public function getAttendance(): array
    {
        return $this->zkTeco->getAttendance();
    }

    public function finalize(): void
    {
        $this->zkTeco->enableDevice();
        $this->zkTeco->disconnect();
    }
}
