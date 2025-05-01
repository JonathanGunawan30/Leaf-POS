<?php

namespace App\Exceptions;

use Exception;

class AddressNotFound extends Exception
{
    public function __construct($message = "Address not found", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
