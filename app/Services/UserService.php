<?php

namespace App\Services;

use App\Mail\ResetTemporaryPassword;
use App\Mail\UserRegistered;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserService
{
    private const PASSWORD_LENGTH = 6;

    private const EXPIRATION_MINUTES = 60;

    private const BUSINESS_ADMIN_ROLE = 'business-admin';

    public function employeeResetPassword(User $user): string
    {
        $temporaryPassword = Str::random(self::PASSWORD_LENGTH);
        $user->password = bcrypt($temporaryPassword);
        $user->save();
        return $temporaryPassword;
    }

    public function create(Collection $companies, array $userData): User
    {
        $username = $this->generateUniqueUsername($companies, $userData['first_name'], $userData['last_name']);
        list($temporaryPassword, $temporaryPasswordExpiresAt) = $this->generateTemporaryPassword();

        $userData['username'] = $username;
        $userData['password'] = bcrypt($temporaryPassword);
        $userData['temporary_password'] = $temporaryPassword;
        $userData['temporary_password_expires_at'] = $temporaryPasswordExpiresAt;

        $user = User::create($userData);
        $user->temporary_password = $temporaryPassword;

        // Mail::to($user->email_address)->send(new UserRegistered($user));
        if ($this->isRoleBusinessAdmin($userData)) {
            $role = Role::where('name', self::BUSINESS_ADMIN_ROLE)->first();
            $user->assignRole($role);
        }
        foreach ($companies as $company) {
            $company->users()->attach($user->id);
        }
        return $user;
    }

    public function generateUniqueUsername(Collection $companies, string $firstName, string $lastName): string
    {
        $firstNameParts = explode(' ', $firstName);
        $firstNameInitial = substr($firstNameParts[0], 0, 1);
        if (count($firstNameParts) > 1) {
            $firstNameInitial .= substr($firstNameParts[1], 0, 1);
        }
        $username = strtolower($firstNameInitial . str_replace(' ', '', strtolower($lastName)));
        foreach ($companies as $company) {
            $usernameExistsInCompany = $company->users()->where('username', $username)->exists();
            if ($usernameExistsInCompany) {
                $i = 1;
                $originalUsername = $username;
                do {
                    $username = $originalUsername . $i;
                    $usernameExistsInCompany = $company->users()->where('username', $username)->exists();
                    $i++;
                } while ($usernameExistsInCompany);
            }
        }
        return $username;
    }

    public function generateTemporaryPassword(): array
    {
        return [Str::random(self::PASSWORD_LENGTH), now()->addMinutes(self::EXPIRATION_MINUTES)];
    }

    public function temporaryPasswordReset(User $user): User
    {
        list($temporaryPassword, $temporaryPasswordExpiresAt) = $this->generateTemporaryPassword();
        $user->temporary_password = $temporaryPassword;
        $user->temporary_password_expires_at = $temporaryPasswordExpiresAt;
        $user->save();
        Mail::to($user->email_address)->send(new ResetTemporaryPassword($user));
        return $user;
    }

    public function getExistingUser(array $userData): User
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
