<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
class UserDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?string $email,
        #[Assert\NotBlank]
        public readonly ?string $password
    )
    {
    }
}