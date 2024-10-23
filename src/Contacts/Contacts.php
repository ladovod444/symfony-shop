<?php

namespace App\Contacts;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class Contacts
{
    public function __construct(
        #[Assert\NotBlank(message: 'Заголовок обязателен для заполнения')]
        public ?string $title = null,
        #[Assert\NotBlank(message: 'Поле "Сообщение" обязателено для заполнения')]
        public ?string $body = null,
        #[Assert\NotBlank(message: 'Поле "Email" обязателено для заполнения')]
        public ?string $email = null,
        private ?User  $user = null,)
    {

    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }
}