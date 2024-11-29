<?php

namespace App\Exceptions;

class ProductNotFound extends \RuntimeException
{
    public function __construct(int $productId)
    {
        parent::__construct("Product with id $productId not found");
    }
}
