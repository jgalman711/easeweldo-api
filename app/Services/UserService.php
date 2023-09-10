<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserService
{
    private const PASSWORD_LENGTH = 6;

    private const BUSINESS_ADMIN_ROLE = 'business-admin';

    public function employeeResetPassword(User $user): string
    {
        $temporaryPassword = Str::random(self::PASSWORD_LENGTH);
        $user->password = bcrypt($temporaryPassword);
        $user->save();
        return $temporaryPassword;
    }

    public function create(Company $company, array $userData): User
    {
        $username = $this->generateUniqueUsername($company, $userData['first_name'], $userData['last_name']);
        $temporaryPassword = Str::random(self::PASSWORD_LENGTH);
        $userData['username'] = $username;
        $userData['password'] = bcrypt($temporaryPassword);
        $user = User::create($userData);
        $user->temporary_password = $temporaryPassword;
        if ($this->isRoleBusinessAdmin($userData)) {
            $role = Role::where('name', self::BUSINESS_ADMIN_ROLE)->first();
            $user->assignRole($role);
        }
        $company->users()->attach($user->id);
        return $user;
    }

    public function generateUniqueUsername(Company $company, string $firstName, string $lastName): string
    {
        $firstNameParts = explode(' ', $firstName);
        $firstNameInitial = substr($firstNameParts[0], 0, 1);
        if (count($firstNameParts) > 1) {
            $firstNameInitial .= substr($firstNameParts[1], 0, 1);
        }
        $username = strtolower($firstNameInitial . str_replace(' ', '', strtolower($lastName)));
        $existingUser = $company->users()->where('username', $username)->first();
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

    public function getExistingUser(Company $company, array $userData): User
    {
        $user = User::find($userData['user_id']);
        throw_if($user->employee, new Exception('User is already linked to employee'));
        return $user;
    }

    private function isRoleBusinessAdmin($data): bool
    {
        return isset($data['role']) && $data['role'] == self::BUSINESS_ADMIN_ROLE;
    }
}
