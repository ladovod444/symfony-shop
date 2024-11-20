<?php

namespace App\Message\Retailcrm;

class ProductMessage
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