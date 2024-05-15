<?php

namespace App\StateMachines\Contracts;

interface LeaveStateContract
{
    public function approve(): void;

    public function decline(): void;

    public function discard(): void;
}
