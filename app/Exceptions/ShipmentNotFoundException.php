<?php

namespace App\Exceptions;

use Exception;

class ShipmentNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct("Shipment not found");
    }
}
