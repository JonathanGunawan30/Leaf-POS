<?php

namespace App\Exceptions;

use Exception;

class RestoreException extends Exception
{
    protected array $details;

    public function __construct(string $message = "", array $details = [], int $code = 0)
    {
        parent::__construct($message, $code);
        $this->details = $details;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
