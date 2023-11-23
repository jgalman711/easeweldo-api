<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Password
{
    private const PASSWORD_LENGTH = 6;

    private const EXPIRATION_MINUTES = 60;

    public function generateTemporaryPassword(): array
    {
        return [Str::random(self::PASSWORD_LENGTH), now()->addMinutes(self::EXPIRATION_MINUTES)];
    }

}
