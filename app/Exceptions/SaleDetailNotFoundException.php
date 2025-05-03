<?php

namespace App\Exceptions;

use Exception;

class SaleDetailNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct("Sale detail id not found");
    }
}
