<?php

namespace App\StateMachine\Contracts;

interface PayrollStateContract
{
    public function pay(): void;
    public function cancel(): void;
}
