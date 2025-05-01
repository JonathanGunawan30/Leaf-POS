<?php

namespace App\Exceptions;

use Exception;

class ExpenseForceException extends Exception
{
    public static function notDeleted(): self
    {
        return new self("Expense is not deleted, so it cannot be hard delete.", 400);
    }
}
