<?php

namespace App\Dto;

use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Validator\Constraints as Assert;
class OrderItemDto
{
    public function __construct(
        //#[MapEntity(Product::class)]
        public readonly ?string $product,
        public readonly ?string  $order,
        #[Assert\NotBlank]
        public readonly ?string  $quantity,
        )
    {
    }
}