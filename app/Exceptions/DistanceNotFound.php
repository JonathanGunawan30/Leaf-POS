<?php

namespace App\Exceptions;

use Exception;

class DistanceNotFound extends Exception
{
    public function __construct($message = "Distance not found", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
