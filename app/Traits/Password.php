<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Password
{
    public function generateTemporaryPassword(): array
    {
        return [
            Str::random(6),
            now()->addMinutes(60),
        ];
    }
}
