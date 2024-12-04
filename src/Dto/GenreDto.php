<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
class GenreDto
{
    public function __construct(
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?int $id,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $title,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'user_order:api:list', 'order:api:list'])]
        public readonly ?string $slug
    )
    {
    }
}