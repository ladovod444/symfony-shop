<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
class ProductDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?string $title,
        #[Assert\NotBlank]
        public readonly ?string $description,
        #[Assert\NotBlank]
        public readonly ?string $sku,
        #[Assert\NotBlank]
        public readonly ?string $current_price,
        #[Assert\NotBlank]
        public readonly ?string $regular_price,
        #[Assert\NotBlank]
        public readonly ?string $image)
    {
    }
}