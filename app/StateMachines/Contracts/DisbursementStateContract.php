<?php

namespace App\StateMachines\Contracts;

interface DisbursementStateContract
{
    public function initialize(): void;
    public function pay(): void;
    public function cancel(): void;
}
