<?php

namespace App\Exceptions;

use Exception;

class CustomerNotFound extends Exception
{
    public function __construct($message = "Customer not found", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
