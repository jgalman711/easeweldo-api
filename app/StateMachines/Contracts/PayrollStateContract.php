<?php

namespace App\StateMachines\Contracts;

interface PayrollStateContract
{
    public function pay(): void;
    public function cancel(): void;
}
