<?php

namespace App\Exceptions;

use Exception;

class CourierNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct("Courier not found");
    }
}
