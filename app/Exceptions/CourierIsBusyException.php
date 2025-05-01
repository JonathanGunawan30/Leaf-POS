<?php

namespace App\Exceptions;

use Exception;

class CourierIsBusyException extends Exception
{
    public function __construct()
    {
        parent::__construct("The courier is currently handling another delivery");
    }
}
