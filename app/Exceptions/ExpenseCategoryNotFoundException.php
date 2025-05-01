<?php

namespace App\Exceptions;

use Exception;

class ExpenseCategoryNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct("Expense category not found");
    }
}
