<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Password
{
    public function generateTemporaryPassword(): string
    {
        return strtoupper(Str::random(6));
    }
}
