<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
class ProductDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $title,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $description,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $sku,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $current_price,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $regular_price,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $image
    )
    {
    }
}