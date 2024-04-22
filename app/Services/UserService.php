<?php

namespace App\Services;

use App\Mail\UserRegistered;
use App\Models\Company;
use App\Models\User;
use App\Traits\Password;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserService
{
    use Password;

    public function create(Company $company, array $userData): User
    {
        $temporaryPassword = $this->generateTemporaryPassword();
        $userData['password'] = bcrypt($temporaryPassword);
        $userData['temporary_password'] = $temporaryPassword;
        $userData['temporary_password_expires_at'] = now()->addMinutes(60);

        $user = User::create($userData);
        $user->temporary_password = $temporaryPassword;

        try {
            Mail::to($user->email_address)->send(new UserRegistered($company, $user));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        if (isset($userData['role']) && $userData['role']) {
            $user->syncRoles($userData['role']);
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
        $username = strtolower($firstNameInitial.str_replace(' ', '', strtolower($lastName)));
        $usernameExistsInCompany = $company->users()->where('username', $username)->exists();
        if ($usernameExistsInCompany) {
            $i = 1;
            $originalUsername = $username;
            do {
                $username = $originalUsername.$i;
                $usernameExistsInCompany = $company->users()->where('username', $username)->exists();
                $i++;
            } while ($usernameExistsInCompany);
        }

        return $username;
    }

    public function getExistingUser(int $userId): User
    {
        $user = User::find($userId);
        throw_if($user->employee, new Exception('User is already linked to employee'));

        return $user;
    }
}
