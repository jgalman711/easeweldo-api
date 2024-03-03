<?php

namespace App\Exceptions;

use Exception;

class InvalidStateTransitionException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid state transition');
    }
}
