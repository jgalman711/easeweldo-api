<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;

class CustomUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        $user = parent::retrieveByCredentials($credentials);
        if ($user) {
            $user->append('email');
        }

        return $user;
    }
}
