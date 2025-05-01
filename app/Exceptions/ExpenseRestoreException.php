<?php

namespace App\Exceptions;

use Exception;

class ExpenseRestoreException extends Exception
{
    public static function notDeleted(): self
    {
        return new self("Expense is not deleted, so it cannot be restored.", 400);
    }
}
