<?php

namespace App\Message;

class OrderMessage
{
    public function __construct(
        private readonly string $content,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public static function create(string $content): self {
        return new self($content);
    }
}