<?php

namespace App\Exceptions;

use Exception;

class StockNotEnoughException extends Exception
{
    public function __construct(string $productName)
    {
        parent::__construct("Insufficient stock for product {$productName}.");
    }
}
