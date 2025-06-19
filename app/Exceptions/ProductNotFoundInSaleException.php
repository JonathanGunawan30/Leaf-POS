<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ProductNotFoundInSaleException extends Exception
{
    protected $productId;

    public function __construct(string $productId, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: "Product ID {$productId} not found in the sale details.";
        parent::__construct($message, $code, $previous);
        $this->productId = $productId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}
