<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
class UserDto
{
    public function __construct(
        #[Groups(['products:api:list', 'order:api:list'])]
        public readonly ?int $id,
        #[Assert\NotBlank]
        #[Groups(['products:api:list', 'order:api:list'])]
        public readonly ?string $email,
//        #[Assert\NotBlank]
        public readonly ?string $password,
        #[Groups(['products:api:list', 'order:api:list'])]
        public readonly ?string $first_name,
        #[Groups(['products:api:list', 'order:api:list'])]
        public readonly ?string $last_name,
    )
    {
    }
}