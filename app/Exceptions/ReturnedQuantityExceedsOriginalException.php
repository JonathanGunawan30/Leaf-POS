<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ReturnedQuantityExceedsOriginalException extends Exception
{
    protected $productId;
    protected $returnedQuantity;
    protected $originalQuantity;

    public function __construct(string $productId, int $returnedQuantity, int $originalQuantity, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: "Returned quantity ({$returnedQuantity}) for product ID {$productId} exceeds original purchased quantity ({$originalQuantity}).";
        parent::__construct($message, $code, $previous);
        $this->productId = $productId;
        $this->returnedQuantity = $returnedQuantity;
        $this->originalQuantity = $originalQuantity;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getReturnedQuantity(): int
    {
        return $this->returnedQuantity;
    }

    public function getOriginalQuantity(): int
    {
        return $this->originalQuantity;
    }
}
