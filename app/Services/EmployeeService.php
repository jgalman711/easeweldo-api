<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class EmployeeService
{
    private const BUSINESS_ADMIN_ROLE = 'business-admin';

    private $message = 'Employee created successfully.';

    public function create(array $data): Employee
    {
        try {
            DB::beginTransaction();
            $employee = Employee::create($data);
            if ($this->isRoleBusinessAdmin($data)) {
                $role = Role::where('name', self::BUSINESS_ADMIN_ROLE)->first();
                $temporaryPassword = Str::random(6);
                $data['password'] = bcrypt($temporaryPassword);
                $data['employee_id'] = $employee->id;
                $user = User::create($data);
                $user->assignRole($role);
                $this->message .= ' The business admin temporary password is ' . $temporaryPassword;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $employee;
    }

    public function getEmployeeCreationMessage(): string
    {
        return $this->message;
    }

    private function isRoleBusinessAdmin($data): bool
    {
        return isset($data['role']) && $data['role'] == self::BUSINESS_ADMIN_ROLE;
    }
}
