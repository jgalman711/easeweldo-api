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

    private const PASSWORD_LENGTH = 6;

    protected $credentials;

    public function create(array $data): Employee
    {
        try {
            DB::beginTransaction();
            $employee = Employee::create($data);
            $username = $this->generateUniqueUsername($employee);
            $temporaryPassword = Str::random(self::PASSWORD_LENGTH);
            $data['username'] = $username;
            $data['password'] = bcrypt($temporaryPassword);
            $data['employee_id'] = $employee->id;
            $user = User::create($data);
            if ($this->isRoleBusinessAdmin($data)) {
                $role = Role::where('name', self::BUSINESS_ADMIN_ROLE)->first();
                $user->assignRole($role);
            }
            $this->credentials = [
                'username' => $username,
                'password' => $temporaryPassword
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $employee;
    }

    public function getEmployeeTemporaryCredentials(): array
    {
        return $this->credentials;
    }

    public function generateUniqueUsername(Employee $employee)
    {
        $company = $employee->company;

        $firstName = $employee->first_name;
        $firstNameParts = explode(' ', $firstName);
        $firstNameInitial = substr($firstNameParts[0], 0, 1);
        if (count($firstNameParts) > 1) {
            $firstNameInitial .= substr($firstNameParts[1], 0, 1);
        }
        $username = strtolower($firstNameInitial . str_replace(' ', '', strtolower($employee->last_name)));

        $existingUser = User::where('username', $username)
            ->whereHas('employee', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->first();

        $usernameExists = $existingUser !== null;
        if ($usernameExists) {
            $i = 1;
            $originalUsername = $username;
            do {
                $username = $originalUsername . $i;
                $existingUser = User::where('username', $username)
                    ->whereHas('employee', function ($query) use ($company) {
                        $query->where('company_id', $company->id);
                    })->first();
                $usernameExists = $existingUser !== null;
                $i++;
            } while ($usernameExists);
        }
        return $username;
    }

    private function isRoleBusinessAdmin($data): bool
    {
        return isset($data['role']) && $data['role'] == self::BUSINESS_ADMIN_ROLE;
    }
}
