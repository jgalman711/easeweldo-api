<?php

namespace App\Interfaces;

use App\Models\Employee;

interface BiometricsStrategy
{
    public function connect();

    public function disableDevice();

    public function addEmployee(Employee $employee);

    public function deleteEmployee(Employee $employee);

    public function getAttendance();
}
